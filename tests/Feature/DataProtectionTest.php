<?php

namespace Tests\Feature;

use App\Models\DaphniaMagnaTemplate;
use App\Models\ReceptionTemplate;
use App\Models\SampleEntry;
use App\Models\User;
use Database\Seeders\DemoDataSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

// Problemas encontrados en la revisión previa a la marcha blanca: se podían perder o alterar datos
class DataProtectionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $analist;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(DemoDataSeeder::class);
        $this->admin = User::where('email', 'areamanager@a')->firstOrFail();
        $this->analist = User::where('email', 'analist@a')->firstOrFail();
    }

    // Antes, eliminar una muestra borraba todas las que se registraron en el mismo lote
    public function test_deleting_a_reception_keeps_the_rest_of_its_batch()
    {
        $this->actingAs($this->admin)->post(route('receptions.store'), [
            'title' => 'Recepción', 'version' => '01', 'client' => 'Cliente de prueba',
            'samples' => [
                ['sample_identifier' => 'A', 'internal_sample_code' => 'L-1'],
                ['sample_identifier' => 'B', 'internal_sample_code' => 'L-2'],
            ],
        ]);
        $first = ReceptionTemplate::where('internal_sample_code', 'L-1')->firstOrFail();

        $this->actingAs($this->admin)->delete(route('receptions.destroy', $first))->assertRedirect();

        $this->assertNull(ReceptionTemplate::find($first->id));
        $this->assertTrue(ReceptionTemplate::where('internal_sample_code', 'L-2')->exists());
    }

    public function test_reception_with_an_entered_sample_cannot_be_deleted()
    {
        $reception = SampleEntry::firstOrFail()->reception;

        $this->actingAs($this->admin)
            ->delete(route('receptions.destroy', $reception))
            ->assertSessionHas('error');

        $this->assertNotNull(ReceptionTemplate::find($reception->id));
    }

    // Antes, un Analista podía borrar una muestra completada junto con sus bioensayos validados
    public function test_only_admins_delete_sample_entries_and_never_with_validated_bioassays()
    {
        $completed = SampleEntry::all()->first(fn ($entry) => $entry->isCompleted());
        $pending = SampleEntry::all()->first(fn ($entry) => ! $entry->hasValidatedBioassays());

        $this->actingAs($this->analist)->delete(route('sample_entries.destroy', $pending))->assertForbidden();
        $this->actingAs($this->admin)->delete(route('sample_entries.destroy', $completed))->assertSessionHas('error');
        $this->assertNotNull(SampleEntry::find($completed->id));

        // Sin bioensayos validados: se elimina junto con sus bioensayos
        $bioassays = $pending->assignedBioassays()->filter();
        $this->actingAs($this->admin)->delete(route('sample_entries.destroy', $pending))->assertRedirect();
        $this->assertNull(SampleEntry::find($pending->id));
        $bioassays->each(fn ($bioassay) => $this->assertNull($bioassay->fresh()));
    }

    // Antes "Corregir" nunca guardaba: la versión "01" no pasaba la regla de número entero
    public function test_sample_entry_can_be_corrected_but_its_code_cannot_change()
    {
        $entry = SampleEntry::firstOrFail();
        $code = $entry->internal_sample_code;

        $this->actingAs($this->analist)
            ->put(route('sample_entries.update', $entry), [
                'title' => $entry->template->title, 'version' => '01',
                'ph' => '7.4', 'internal_sample_code' => 'OTRO-CODIGO',
            ])
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('sample_entries.index'));

        $this->assertEquals(7.4, $entry->refresh()->ph);
        $this->assertSame($code, $entry->internal_sample_code);

        $this->actingAs($this->analist)
            ->put(route('sample_entries.update', $entry), ['title' => 'T', 'version' => '01', 'ph' => 'abc'])
            ->assertSessionHasErrors('ph');
    }

    public function test_user_can_change_their_own_password()
    {
        $this->actingAs($this->analist)->get(route('account.password'))->assertOk();

        $this->actingAs($this->analist)
            ->put(route('account.password.update'), [
                'current_password' => 'no-es-la-actual', 'password' => 'clave-nueva-123', 'password_confirmation' => 'clave-nueva-123',
            ])
            ->assertSessionHasErrors('current_password');

        $this->analist->update(['password' => Hash::make('clave-anterior-1')]);
        $this->actingAs($this->analist)
            ->put(route('account.password.update'), [
                'current_password' => 'clave-anterior-1', 'password' => 'clave-nueva-123', 'password_confirmation' => 'clave-nueva-123',
            ])
            ->assertSessionHasNoErrors();

        $this->assertTrue(Hash::check('clave-nueva-123', $this->analist->fresh()->password));
    }

    public function test_error_pages_and_validation_messages_are_in_spanish()
    {
        $this->actingAs($this->analist)->get('/users')
            ->assertForbidden()
            ->assertSee('No tienes permiso')
            ->assertDontSee('User does not have the right roles');

        $this->actingAs($this->analist)->get('/pagina-que-no-existe')
            ->assertNotFound()
            ->assertSee('Página no encontrada');

        // El motivo que da la policy se muestra tal cual
        $validated = DaphniaMagnaTemplate::whereNotNull('validated_at')->firstOrFail();
        $this->actingAs($this->analist)
            ->put(route('daphnia-magna.update', $validated), ['sample' => $validated->sample])
            ->assertForbidden()
            ->assertSee('Este bioensayo ya fue validado');

        $this->actingAs($this->admin)
            ->put(route('sample_entries.update', SampleEntry::firstOrFail()), ['title' => 'T', 'version' => '01', 'ph' => 'abc'])
            ->assertSessionHasErrors(['ph' => 'El pH debe ser un número.']);
    }

    public function test_system_clock_uses_chile_time()
    {
        $this->assertSame('America/Santiago', config('app.timezone'));
        $this->assertSame('America/Santiago', now()->timezoneName);
    }
}
