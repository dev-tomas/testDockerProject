 <?php

 use App\SunatCode;
 use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(CoinsSeeder::class);
        $this->call(MeasuresSeeder::class);
        $this->call(PermissionsTableSeeder::class);
        $this->call(PlansTableSeeder::class);
        $this->call(TypeAffectationsSeeder::class);
        $this->call(TypeVouchersSeeder::class);
        $this->call(TypeDocumentsSeeder::class);
        $this->call(TaxesTableSeeder::class);
        $this->call(OperationTypesSeeder::class);
        $this->call(BankAccountTypeSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(UbigeosSeederTable::class);
        $this->call(SunatCodeSeeder::class);
        $this->call(TypeOperationsTableSeeder::class);
        $this->call(TypeCreditNotesSeeder::class);
        $this->call(TypeDebitNotesSeeder::class);
        $this->call(IconsDashboardSeed::class);
        $this->call(SuperAdminUserSeeder::class);
        $this->call(RegimesTableSeeder::class);
        $this->call(IgvTypeSeeder::class);
        $this->call(ThemesSeeder::class);
        $this->call(PriceListsSeeder::class);
        $this->call(AddServiceToMeasureTableSeed::class);
        $this->call(NewAdminPermissionSeed::class);
        $this->call(ChangeHeadquarterPermissionSeed::class);
        $this->call(ContabilidadPermissionsSeed::class);
        $this->call(CashPermissionSeed::class);
    }
}
