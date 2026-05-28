<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\{DB, Hash};
use App\Models\{User, Docente, Postulante, Carrera, Gestion};
use Spatie\Permission\Models\Role;

/**
 * CupDataSeeder — pobla TODAS las tablas del sistema CUP con datos ficticios.
 * Ejecutar con: php artisan db:seed  o  migrate:fresh --seed
 */
class CupDataSeeder extends Seeder
{
    public function run(): void
    {
        // ── 1. GESTIONES ────────────────────────────────────────────────────
        DB::table('gestiones')->insertOrIgnore([
            ['descripcion'=>'Semestre 1-2024','fecha_inicio'=>'2024-02-01','fecha_fin'=>'2024-06-30','estado'=>'finalizado','created_at'=>now(),'updated_at'=>now()],
            ['descripcion'=>'Semestre 2-2024','fecha_inicio'=>'2024-07-15','fecha_fin'=>'2024-12-15','estado'=>'finalizado','created_at'=>now(),'updated_at'=>now()],
            ['descripcion'=>'Semestre 1-2025','fecha_inicio'=>'2025-02-03','fecha_fin'=>'2025-06-30','estado'=>'finalizado','created_at'=>now(),'updated_at'=>now()],
            ['descripcion'=>'Semestre 2-2025','fecha_inicio'=>'2025-07-14','fecha_fin'=>'2025-12-14','estado'=>'finalizado','created_at'=>now(),'updated_at'=>now()],
            ['descripcion'=>'Semestre 1-2026','fecha_inicio'=>'2026-02-02','fecha_fin'=>'2026-06-30','estado'=>'en_curso','created_at'=>now(),'updated_at'=>now()],
        ]);
        $gestion = DB::table('gestiones')->where('estado','en_curso')->first();

        // ── 2. CARRERAS ──────────────────────────────────────────────────────
        DB::table('carreras')->insertOrIgnore([
            ['nombre'=>'Ingeniería Informática',                  'sigla'=>'INF','descripcion'=>'Desarrollo de software y sistemas computacionales.','estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['nombre'=>'Ingeniería de Sistemas',                  'sigla'=>'SIS','descripcion'=>'Análisis, diseño e implementación de sistemas de información.','estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['nombre'=>'Ingeniería en Redes y Telecomunicaciones','sigla'=>'RYT','descripcion'=>'Diseño e instalación de redes de datos y comunicaciones.','estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['nombre'=>'Ingeniería en Robótica',                  'sigla'=>'ROB','descripcion'=>'Diseño, programación y control de sistemas robóticos.','estado'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);

        // ── 3. MATERIAS ──────────────────────────────────────────────────────
        DB::table('materias')->insertOrIgnore([
            ['nombre'=>'Computación', 'area_formacion'=>'Computación','pond_examen1'=>30,'pond_examen2'=>30,'pond_examen3'=>40,'nota_minima_aprobacion'=>60,'orden'=>1,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['nombre'=>'Matemáticas', 'area_formacion'=>'Matemáticas','pond_examen1'=>30,'pond_examen2'=>30,'pond_examen3'=>40,'nota_minima_aprobacion'=>60,'orden'=>2,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['nombre'=>'Física',      'area_formacion'=>'Física',     'pond_examen1'=>30,'pond_examen2'=>30,'pond_examen3'=>40,'nota_minima_aprobacion'=>60,'orden'=>3,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ['nombre'=>'Inglés',      'area_formacion'=>'Inglés',     'pond_examen1'=>30,'pond_examen2'=>30,'pond_examen3'=>40,'nota_minima_aprobacion'=>60,'orden'=>4,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
        ]);

        $carreras = DB::table('carreras')->get();
        $materias = DB::table('materias')->get();

        // ── 4. CUPOS POR CARRERA Y GESTIÓN (CU-08) ──────────────────────────
        $cuposPorSigla = ['INF'=>80,'SIS'=>75,'RYT'=>60,'ROB'=>50];
        $gestiones = DB::table('gestiones')->get();
        foreach ($gestiones as $g) {
            foreach ($carreras as $c) {
                $cupo = $cuposPorSigla[$c->sigla] ?? 60;
                DB::table('cupos_carrera')->updateOrInsert(
                    ['carrera_id'=>$c->id,'gestion_id'=>$g->id],
                    ['cantidad_maxima'=>$cupo,'created_at'=>now(),'updated_at'=>now()]
                );
            }
        }

        // ── 5. DOCENTES (CU-10) ─────────────────────────────────────────────
        $docentesData = [
            ['ci'=>'4512378','nombres'=>'Roberto','apellidos'=>'Mamani Flores',   'telefono'=>'71234501','email'=>'rmamani@ficct.edu.bo',  'titulo_profesional'=>'Ing. en Informática',              'maestria'=>'Maestría en Ingeniería de Software',            'diplomado_educacion_superior'=>'Diplomado en Docencia Universitaria','certificacion_ingles'=>'B2','area_formacion'=>'Computación'],
            ['ci'=>'5623489','nombres'=>'Carla',   'apellidos'=>'Quispe Vargas',   'telefono'=>'72345602','email'=>'cquispe@ficct.edu.bo',   'titulo_profesional'=>'Lic. en Matemáticas',              'maestria'=>'Maestría en Docencia Universitaria',            'diplomado_educacion_superior'=>'Diplomado en Educación Superior',    'certificacion_ingles'=>null,'area_formacion'=>'Matemáticas'],
            ['ci'=>'6734590','nombres'=>'Pedro',   'apellidos'=>'Condori Huanca',  'telefono'=>'73456703','email'=>'pcondori@ficct.edu.bo',  'titulo_profesional'=>'Lic. en Física',                   'maestria'=>'Maestría en Física Aplicada',                   'diplomado_educacion_superior'=>'Diplomado en Educación Superior',    'certificacion_ingles'=>null,'area_formacion'=>'Física'],
            ['ci'=>'7845601','nombres'=>'Lucia',   'apellidos'=>'Torrez Sánchez',  'telefono'=>'74567804','email'=>'ltorrez@ficct.edu.bo',   'titulo_profesional'=>'Lic. en Lingüística',              'maestria'=>'Maestría en Enseñanza del Inglés',              'diplomado_educacion_superior'=>'Diplomado en Edu. Superior Bilingüe','certificacion_ingles'=>'C1','area_formacion'=>'Inglés'],
            ['ci'=>'8956712','nombres'=>'Julio',   'apellidos'=>'Chávez Montaño',  'telefono'=>'75678905','email'=>'jchavez@ficct.edu.bo',   'titulo_profesional'=>'Ing. de Sistemas',                 'maestria'=>'Maestría en Sistemas de Información',           'diplomado_educacion_superior'=>'Diplomado en Docencia Universitaria','certificacion_ingles'=>'B1','area_formacion'=>'Computación'],
            ['ci'=>'9067823','nombres'=>'Sandra',  'apellidos'=>'Apaza Mamani',    'telefono'=>'76789006','email'=>'sapaza@ficct.edu.bo',    'titulo_profesional'=>'Lic. en Matemáticas',              'maestria'=>'Maestría en Matemática Educativa',              'diplomado_educacion_superior'=>'Diplomado en Educación Superior',    'certificacion_ingles'=>null,'area_formacion'=>'Matemáticas'],
            ['ci'=>'1078934','nombres'=>'Hugo',    'apellidos'=>'Ticona Larico',   'telefono'=>'77890107','email'=>'hticona@ficct.edu.bo',   'titulo_profesional'=>'Lic. en Física',                   'maestria'=>'Maestría en Física Nuclear',                   'diplomado_educacion_superior'=>'Diplomado en Educación Superior',    'certificacion_ingles'=>null,'area_formacion'=>'Física'],
            ['ci'=>'2189045','nombres'=>'Ana',     'apellidos'=>'Beltrán Rojas',   'telefono'=>'78901208','email'=>'abeltran@ficct.edu.bo',  'titulo_profesional'=>'Lic. en Lingüística Aplicada',     'maestria'=>'Maestría en Traducción e Interpretación',      'diplomado_educacion_superior'=>'Diplomado en Educación Superior',    'certificacion_ingles'=>'C2','area_formacion'=>'Inglés'],
            ['ci'=>'3290156','nombres'=>'Carlos',  'apellidos'=>'Villanueva Cruz', 'telefono'=>'79012309','email'=>'cvillanueva@ficct.edu.bo','titulo_profesional'=>'Ing. en Redes y Telecomunicaciones','maestria'=>'Maestría en Redes de Computadoras',            'diplomado_educacion_superior'=>'Diplomado en Docencia Universitaria','certificacion_ingles'=>'B2','area_formacion'=>'Computación'],
            ['ci'=>'4301267','nombres'=>'Maria',   'apellidos'=>'Aliaga Pinto',    'telefono'=>'70123410','email'=>'maliaga@ficct.edu.bo',   'titulo_profesional'=>'Lic. en Matemáticas',              'maestria'=>'Maestría en Estadística Aplicada',             'diplomado_educacion_superior'=>'Diplomado en Educación Superior',    'certificacion_ingles'=>null,'area_formacion'=>'Matemáticas'],
        ];

        foreach ($docentesData as $d) {
            DB::table('docentes')->updateOrInsert(
                ['ci' => $d['ci']],
                array_merge($d, ['estado'=>true,'created_at'=>now(),'updated_at'=>now()])
            );
        }
        $docentes = DB::table('docentes')->get()->keyBy('email');

        // ── 6. USUARIOS para docentes (vinculados) ───────────────────────────
        $rolDocente = Role::where('name','Docente')->first();
        foreach ($docentesData as $d) {
            $doc = $docentes[$d['email']] ?? null;
            if (!$doc) continue;
            $user = User::firstOrCreate(
                ['email' => $d['email']],
                [
                    'name'              => $d['nombres'].' '.$d['apellidos'],
                    'password'          => Hash::make('Docente@2026'),
                    'email_verified_at' => now(),
                    'activo'            => true,
                    'docente_id'        => $doc->id,
                ]
            );
            if ($rolDocente && !$user->hasRole('Docente')) {
                $user->assignRole($rolDocente);
            }
        }

        // ── 7. POSTULANTES (150 ficticios) (CU-05) ──────────────────────────
        $nombres   = ['Juan','María','Carlos','Ana','Luis','Rosa','Jorge','Elena','Miguel','Paola','Ricardo','Sandra','Fernando','Claudia','Daniel','Patricia','Eduardo','Verónica','Andrés','Natalia','Sergio','Valeria','Marcos','Alejandra','Pablo','Camila','Oscar','Fernanda','Iván','Diana'];
        $apellidos = ['Mamani','Quispe','Condori','Flores','García','Torrez','Chávez','Apaza','Ticona','Aliaga','Villanueva','Beltrán','Herrera','Pinto','Rojas','Vega','Soria','Marca','Gutiérrez','Espinoza','Mendoza','Salazar','Barrios','Cáceres','Pérez','López','Miranda','Vargas','Huanca','Cruz'];
        $colegios  = ['Colegio Nacional Bolivia','U.E. San Calixto','U.E. Don Bosco','U.E. La Salle','U.E. Franz Tamayo','Colegio Los Andes','U.E. Ayacucho','U.E. Simón Bolívar','Colegio Hernando Siles','U.E. Sagrado Corazón'];
        $ciudades  = ['La Paz','El Alto','Cochabamba','Santa Cruz','Oruro'];
        $carreraIds = $carreras->pluck('id')->toArray();
        $rolPost    = Role::where('name','Postulante')->first();

        $postInsertados = 0;
        for ($i = 1; $i <= 150; $i++) {
            $ci = (string)(10000000 + $i * 137 + ($i % 99));
            if (DB::table('postulantes')->where('ci',$ci)->exists()) continue;

            $nom = $nombres[($i-1) % count($nombres)];
            $ap1 = $apellidos[($i)   % count($apellidos)];
            $ap2 = $apellidos[($i+5) % count($apellidos)];
            $ops  = $carreraIds;
            shuffle($ops);
            $op1 = $ops[0]; $op2 = $ops[1];
            $nac = \Carbon\Carbon::now()->subYears(rand(17,22))->subDays(rand(0,365))->toDateString();

            $pid = DB::table('postulantes')->insertGetId([
                'gestion_id'         => $gestion->id,
                'primera_opcion_id'  => $op1,
                'segunda_opcion_id'  => $op2,
                'ci'                 => $ci,
                'nombres'            => $nom,
                'apellidos'          => "$ap1 $ap2",
                'fecha_nacimiento'   => $nac,
                'sexo'               => ($i % 2 === 0) ? 'M' : 'F',
                'direccion'          => 'Av. '.($i % 20 + 1).' N° '.rand(100,999),
                'telefono'           => '7'.rand(1000000,9999999),
                'email'              => "postulante{$i}@gmail.com",
                'colegio_procedencia'=> $colegios[$i % count($colegios)],
                'ciudad'             => $ciudades[$i % count($ciudades)],
                'doc_ci'             => true,
                'doc_libreta_colegio'=> true,
                'doc_titulo_bachiller'=> true,
                'estado'             => 'inscrito',
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);

            // Usuario vinculado al postulante
            $email = "postulante{$i}@gmail.com";
            $user = User::firstOrCreate(
                ['email' => $email],
                [
                    'name'              => "$nom $ap1 $ap2",
                    'password'          => Hash::make('Postulante@2026'),
                    'email_verified_at' => now(),
                    'activo'            => true,
                    'postulante_id'     => $pid,
                ]
            );
            if ($rolPost && !$user->hasRole('Postulante')) {
                $user->assignRole($rolPost);
            }
            $postInsertados++;
        }

        // ── 8. GRUPOS (CU-11) — CEIL(150/60) = 3 grupos ─────────────────────
        if (DB::table('grupos')->where('gestion_id',$gestion->id)->count() === 0) {
            DB::table('grupos')->insert([
                ['gestion_id'=>$gestion->id,'codigo'=>'GRP-A','turno'=>'mañana', 'modalidad'=>'presencial','capacidad_maxima'=>60,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
                ['gestion_id'=>$gestion->id,'codigo'=>'GRP-B','turno'=>'tarde',  'modalidad'=>'presencial','capacidad_maxima'=>60,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
                ['gestion_id'=>$gestion->id,'codigo'=>'GRP-C','turno'=>'noche',  'modalidad'=>'virtual',   'capacidad_maxima'=>60,'estado'=>true,'created_at'=>now(),'updated_at'=>now()],
            ]);
        }
        $grupos = DB::table('grupos')->where('gestion_id',$gestion->id)->get();

        // ── 9. INSCRIBIR POSTULANTES A GRUPOS (CU-11) ────────────────────────
        $postIds = DB::table('postulantes')->where('gestion_id',$gestion->id)->pluck('id')->toArray();
        foreach ($postIds as $idx => $pid) {
            $grupoObj = $grupos[$idx % count($grupos)]; // distribución rotativa
            DB::table('grupo_postulante')->updateOrInsert(
                ['grupo_id'=>$grupoObj->id,'postulante_id'=>$pid],
                ['created_at'=>now(),'updated_at'=>now()]
            );
            DB::table('postulantes')->where('id',$pid)->update(['estado'=>'en_curso','updated_at'=>now()]);
        }

        // ── 10. ASIGNACIONES DOCENTE–GRUPO–MATERIA (CU-12) ──────────────────
        //  Comp→Roberto,Julio,Carlos | Mat→Carla,Sandra,Maria | Fis→Pedro,Hugo | Ing→Lucia,Ana
        $docIds = DB::table('docentes')->pluck('id','email')->toArray();
        $matIds = DB::table('materias')->pluck('id','nombre')->toArray();
        $asignMap = [
            // [grupo_idx, materia_nombre, docente_email, dia, h_inicio, h_fin]
            [0,'Computación','rmamani@ficct.edu.bo','lunes',   '07:00','09:00'],
            [0,'Matemáticas','cquispe@ficct.edu.bo', 'lunes',   '09:00','11:00'],
            [0,'Física',     'pcondori@ficct.edu.bo','martes',  '07:00','09:00'],
            [0,'Inglés',     'ltorrez@ficct.edu.bo', 'martes',  '09:00','11:00'],
            [1,'Computación','jchavez@ficct.edu.bo', 'lunes',   '14:00','16:00'],
            [1,'Matemáticas','sapaza@ficct.edu.bo',  'lunes',   '16:00','18:00'],
            [1,'Física',     'hticona@ficct.edu.bo', 'martes',  '14:00','16:00'],
            [1,'Inglés',     'abeltran@ficct.edu.bo','martes',  '16:00','18:00'],
            [2,'Computación','cvillanueva@ficct.edu.bo','miercoles','19:00','21:00'],
            [2,'Matemáticas','maliaga@ficct.edu.bo', 'miercoles','19:00','21:00'],
            [2,'Física',     'pcondori@ficct.edu.bo','jueves',  '19:00','21:00'],
            [2,'Inglés',     'ltorrez@ficct.edu.bo', 'jueves',  '19:00','21:00'],
        ];
        foreach ($asignMap as [$gi,$mat,$email,$dia,$hi,$hf]) {
            $grp = $grupos[$gi] ?? null;
            $mid = $matIds[$mat] ?? null;
            $did = $docIds[$email] ?? null;
            if (!$grp || !$mid || !$did) continue;
            DB::table('asignaciones')->updateOrInsert(
                ['grupo_id'=>$grp->id,'materia_id'=>$mid],
                ['docente_id'=>$did,'dia'=>$dia,'hora_inicio'=>$hi,'hora_fin'=>$hf,'created_at'=>now(),'updated_at'=>now()]
            );
        }

        // ── 11. NOTAS (CU-13 / CU-14) — 3 exámenes × 4 materias × 150 postulantes
        $matList = DB::table('materias')->get();
        $gpMap   = DB::table('grupo_postulante')->get()->groupBy('postulante_id');

        $postAll = DB::table('postulantes')->where('gestion_id',$gestion->id)->pluck('id')->toArray();
        foreach ($postAll as $idx => $pid) {
            $aprueba = (($idx % 10 !== 0) && ($idx % 7 !== 0)); // ~70% aprueban
            $gpRows  = $gpMap->get($pid);
            $grupoId = $gpRows ? $gpRows->first()->grupo_id : $grupos[0]->id;

            foreach ($matList as $mat) {
                if (DB::table('notas')->where(['postulante_id'=>$pid,'materia_id'=>$mat->id,'grupo_id'=>$grupoId])->exists()) continue;

                if ($aprueba) {
                    $n1 = rand(62,92); $n2 = rand(60,95); $n3 = rand(63,98);
                } else {
                    if ($mat->nombre === 'Matemáticas') {
                        $n1 = rand(20,55); $n2 = rand(18,52); $n3 = rand(22,58);
                    } else {
                        $n1 = rand(55,75); $n2 = rand(50,72); $n3 = rand(52,74);
                    }
                }
                $nf = round($n1*0.30 + $n2*0.30 + $n3*0.40, 2);
                DB::table('notas')->insert([
                    'postulante_id'=>$pid,'materia_id'=>$mat->id,'grupo_id'=>$grupoId,
                    'examen1'=>$n1,'examen2'=>$n2,'examen3'=>$n3,
                    'nota_final'=>$nf,'aprobado'=>($nf>=60),
                    'created_at'=>now(),'updated_at'=>now(),
                ]);
            }

            // Actualizar promedio y estado del postulante
            $notasPost = DB::table('notas')->where('postulante_id',$pid)->get();
            $promedio  = round($notasPost->avg('nota_final'), 2);
            $estadoPos = $notasPost->every(fn($n)=>$n->nota_final>=60) ? 'aprobado' : 'no_aprobado';
            DB::table('postulantes')->where('id',$pid)->update([
                'promedio_general'=>$promedio,'estado'=>$estadoPos,'updated_at'=>now()
            ]);
        }

        // ── 12. ADMISIONES (CU-16 / CU-17 / CU-18) ──────────────────────────
        $cuposDB = DB::table('cupos_carrera')->where('gestion_id',$gestion->id)->pluck('cantidad_maxima','carrera_id')->toArray();
        $contadores = array_fill_keys(array_keys($cuposDB), 0);

        $aprobados = DB::table('postulantes')
            ->where('gestion_id',$gestion->id)
            ->where('estado','aprobado')
            ->orderByDesc('promedio_general')
            ->get();

        $pendientes2 = [];
        foreach ($aprobados as $p) {
            if (DB::table('admisiones')->where('postulante_id',$p->id)->exists()) continue;
            $c1 = $p->primera_opcion_id;
            if (($contadores[$c1] ?? 0) < ($cuposDB[$c1] ?? 0)) {
                DB::table('admisiones')->insert(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>$c1,'resultado'=>'admitido_primera','publicado'=>true,'created_at'=>now(),'updated_at'=>now()]);
                $contadores[$c1]++;
                DB::table('postulantes')->where('id',$p->id)->update(['estado'=>'admitido','updated_at'=>now()]);
            } else {
                $pendientes2[] = $p;
            }
        }
        foreach ($pendientes2 as $p) {
            $c2 = $p->segunda_opcion_id;
            if (($contadores[$c2] ?? 0) < ($cuposDB[$c2] ?? 0)) {
                DB::table('admisiones')->insert(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>$c2,'resultado'=>'admitido_segunda','publicado'=>true,'created_at'=>now(),'updated_at'=>now()]);
                $contadores[$c2]++;
                DB::table('postulantes')->where('id',$p->id)->update(['estado'=>'admitido_segunda_opcion','updated_at'=>now()]);
            } else {
                DB::table('admisiones')->insert(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>null,'resultado'=>'no_admitido','publicado'=>true,'created_at'=>now(),'updated_at'=>now()]);
                DB::table('postulantes')->where('id',$p->id)->update(['estado'=>'no_admitido','updated_at'=>now()]);
            }
        }
        // Reprobados sin admisión
        DB::table('postulantes')->where('gestion_id',$gestion->id)->where('estado','no_aprobado')
            ->chunkById(50, function($chunk) use ($gestion) {
                foreach ($chunk as $p) {
                    if (DB::table('admisiones')->where('postulante_id',$p->id)->exists()) continue;
                    DB::table('admisiones')->insert(['postulante_id'=>$p->id,'gestion_id'=>$gestion->id,'promedio_general'=>$p->promedio_general,'carrera_asignada_id'=>null,'resultado'=>'no_admitido','publicado'=>false,'created_at'=>now(),'updated_at'=>now()]);
                }
            });

        $this->command->info('  CupDataSeeder completado ✔');
    }
}
