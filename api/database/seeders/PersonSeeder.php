<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Person;

class PersonSeeder extends Seeder
{
    public function run(): void
    {
        Person::create(['meno' => 'Ján B', 'vek' => 29, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Petra G', 'vek' => 30, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Martin S', 'vek' => 27, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Oliver O', 'vek' => 25, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Simona P', 'vek' => 19, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Peter A', 'vek' => 47, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Katarína M', 'vek' => 34, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Michal K', 'vek' => 41, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Zuzana V', 'vek' => 28, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Tomáš H', 'vek' => 33, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Lucia D', 'vek' => 22, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Andrej T', 'vek' => 38, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Mária J', 'vek' => 45, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Filip L', 'vek' => 26, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Veronika C', 'vek' => 31, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Erik N', 'vek' => 20, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Sofia R', 'vek' => 29, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Daniel P', 'vek' => 35, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Elena F', 'vek' => 40, 'pohlavie' => 'žena']);
        Person::create(['meno' => 'Patrik Z', 'vek' => 23, 'pohlavie' => 'muž']);
        Person::create(['meno' => 'Natália B', 'vek' => 37, 'pohlavie' => 'žena']);
    }
}
