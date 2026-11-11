<?php

namespace Database\Seeders;

use App\Enums\AttendanceMethod;
use App\Enums\AttendanceStatus;
use App\Enums\FollowUpStatus;
use App\Enums\PointType;
use App\Enums\SemesterType;
use App\Enums\StudentStatus;
use App\Enums\ViolationSeverity;
use App\Models\AcademicYear;
use App\Models\Attendance;
use App\Models\AttendanceSetting;
use App\Models\Major;
use App\Models\PointCategory;
use App\Models\PointTransaction;
use App\Models\RemedialPoint;
use App\Models\SchoolClass;
use App\Models\SchoolSetting;
use App\Models\Semester;
use App\Models\Student;
use App\Models\StudentQRCode;
use App\Models\User;
use App\Models\ViolationFollowUp;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Admin User
        $admin = User::firstOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Administrator SMK Tri Sukses Natar',
                'email' => 'admin@smktrisuksesnatar.sch.id',
                'password' => Hash::make('admin123'),
            ]
        );

        // 2. School Profile
        SchoolSetting::current();

        // 3. Attendance Settings
        AttendanceSetting::current();

        // 4. Academic Year & Semester
        $academicYear = AcademicYear::firstOrCreate(
            ['name' => '2024/2025'],
            [
                'start_date' => '2024-07-15',
                'end_date' => '2025-06-25',
                'is_active' => true,
            ]
        );

        $semesterGanjil = Semester::firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'semester_type' => SemesterType::GANJIL,
            ],
            [
                'is_active' => true,
                'is_reset_completed' => false,
            ]
        );

        Semester::firstOrCreate(
            [
                'academic_year_id' => $academicYear->id,
                'semester_type' => SemesterType::GENAP,
            ],
            [
                'is_active' => false,
                'is_reset_completed' => false,
            ]
        );

        // 5. Majors (Jurusan)
        $tkj = Major::firstOrCreate(
            ['code' => 'TKJ'],
            ['name' => 'Teknik Komputer dan Jaringan', 'description' => 'Jurusan Jaringan, Komputer, dan Sistem Operasi', 'is_active' => true]
        );

        $tkr = Major::firstOrCreate(
            ['code' => 'TKR'],
            ['name' => 'Teknik Kendaraan Ringan Otomotif', 'description' => 'Jurusan Otomotif dan Mesin Kendaraan', 'is_active' => true]
        );

        $ak = Major::firstOrCreate(
            ['code' => 'AK'],
            ['name' => 'Akuntansi dan Keuangan Lembaga', 'description' => 'Jurusan Keuangan dan Pembukuan', 'is_active' => true]
        );

        // 6. School Classes (Kelas)
        $class1 = SchoolClass::firstOrCreate(
            ['name' => 'X TKJ 1'],
            ['major_id' => $tkj->id, 'academic_year_id' => $academicYear->id, 'grade_level' => '10', 'is_active' => true]
        );

        $class2 = SchoolClass::firstOrCreate(
            ['name' => 'XI TKR 1'],
            ['major_id' => $tkr->id, 'academic_year_id' => $academicYear->id, 'grade_level' => '11', 'is_active' => true]
        );

        $class3 = SchoolClass::firstOrCreate(
            ['name' => 'XII AK 1'],
            ['major_id' => $ak->id, 'academic_year_id' => $academicYear->id, 'grade_level' => '12', 'is_active' => true]
        );

        // 7. Official Point Categories of SMK Tri Sukses Natar
        $categoriesData = [
            // PRESTASI (+)
            [
                'type' => PointType::PRESTASI,
                'severity' => null,
                'category_name' => 'Jujur & Beradab',
                'action_name' => 'Mengembalikan barang temuan / mengakui kesalahan secara jujur',
                'min_points' => 10,
                'max_points' => 20,
                'default_points' => 15,
                'description' => 'Siswa menunjukkan integritas tinggi dengan mengembalikan barang atau inisiatif membantu guru.',
            ],
            [
                'type' => PointType::PRESTASI,
                'severity' => null,
                'category_name' => 'Disiplin Maksimal',
                'action_name' => 'Kehadiran 100% tanpa Alpa, Izin, atau Sakit dalam satu bulan',
                'min_points' => 20,
                'max_points' => 20,
                'default_points' => 20,
                'description' => 'Konsistensi kehadiran sempurna selama 1 bulan penuh.',
            ],
            [
                'type' => PointType::PRESTASI,
                'severity' => null,
                'category_name' => 'Tanggung Jawab',
                'action_name' => 'Rajin piket kelas / Pengurus kelas atau OSIS amanah / Taat amal sholeh',
                'min_points' => 5,
                'max_points' => 30,
                'default_points' => 15,
                'description' => 'Menjalankan amanah kepengurusan dan amal sholeh dengan baik.',
            ],
            [
                'type' => PointType::PRESTASI,
                'severity' => null,
                'category_name' => 'Prestasi Lomba / Kelas',
                'action_name' => 'Juara Kelas / Juara Lomba tingkat Sekolah, Daerah, atau Nasional',
                'min_points' => 20,
                'max_points' => 100,
                'default_points' => 50,
                'description' => 'Meraih juara akademik maupun non-akademik membawa nama harum sekolah.',
            ],

            // PELANGGARAN (-)
            // 1. Ringan - Kedisiplinan Umum (-5)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::RINGAN,
                'category_name' => 'Ringan - Kedisiplinan Umum',
                'action_name' => 'Terlambat masuk sekolah atau membuang sampah sembarangan',
                'min_points' => 5,
                'max_points' => 5,
                'default_points' => 5,
                'description' => 'Pelanggaran kedisiplinan umum ringan.',
            ],
            // 2. Ringan - Atribut & Seragam Putra (-5)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::RINGAN,
                'category_name' => 'Ringan - Atribut & Seragam Putra',
                'action_name' => 'Celana tidak sesuai standar, atribut tidak lengkap (sabuk, kaos kaki, bet), baju dikeluarkan',
                'min_points' => 5,
                'max_points' => 5,
                'default_points' => 5,
                'description' => 'Kelengkapan dan kerapihan seragam putra.',
            ],
            // 3. Ringan - Kerapihan Putra (-5)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::RINGAN,
                'category_name' => 'Ringan - Kerapihan Putra',
                'action_name' => 'Rambut panjang/tidak rapi, mewarnai rambut, memakai aksesoris (gelang, kalung, cincin)',
                'min_points' => 5,
                'max_points' => 5,
                'default_points' => 5,
                'description' => 'Standar potongan rambut dan aksesoris putra.',
            ],
            // 4. Ringan - Seragam & Atribut Putri (-5)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::RINGAN,
                'category_name' => 'Ringan - Seragam & Atribut Putri',
                'action_name' => 'Tidak memakai jilbab resmi sekolah, tanpa ciput, tanpa deker tangan/kaki',
                'min_points' => 5,
                'max_points' => 5,
                'default_points' => 5,
                'description' => 'Ketentuan jilbab dan kelengkapan seragam putri.',
            ],
            // 5. Sedang (-10 s/d -20)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::SEDANG,
                'category_name' => 'Pelanggaran Sedang',
                'action_name' => 'Membolos pelajaran, membawa HP tanpa izin, berkata kotor/kasar, make up berlebihan',
                'min_points' => 10,
                'max_points' => 20,
                'default_points' => 15,
                'description' => 'Pelanggaran tingkat sedang yang memerlukan konseling dan surat pernyataan.',
            ],
            // 6. Berat (-50 s/d -75)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::BERAT,
                'category_name' => 'Pelanggaran Berat',
                'action_name' => 'Merokok, berkelahi/tawuran, membangkang guru, mencuri, kabur dari sekolah/asrama',
                'min_points' => 50,
                'max_points' => 75,
                'default_points' => 50,
                'description' => 'Pelanggaran berat yang berakibat pemanggilan orang tua dan SP 1 / SP 2 / SP 3.',
            ],
            // 7. Fatal (-100)
            [
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::FATAL,
                'category_name' => 'Pelanggaran Fatal',
                'action_name' => 'Narkoba, minuman keras, tindakan asusila, tindak kriminalitas',
                'min_points' => 100,
                'max_points' => 100,
                'default_points' => 100,
                'description' => 'Pelanggaran fatal: Konferensi kasus khusus, sidang pleno, pengembalian ke orang tua/hukum.',
            ],
        ];

        $createdCategories = [];
        foreach ($categoriesData as $cat) {
            $createdCategories[$cat['category_name']] = PointCategory::firstOrCreate(
                ['category_name' => $cat['category_name'], 'action_name' => $cat['action_name']],
                $cat
            );
        }

        // 8. 20 Demo Students
        $studentsData = [
            // Kelas X TKJ 1
            ['nis' => '240101', 'nisn' => '0071234561', 'name' => 'Achmad Fauzi Nugraha', 'gender' => 'L', 'place' => 'Lampung Selatan', 'dob' => '2008-03-12', 'class_id' => $class1->id],
            ['nis' => '240102', 'nisn' => '0071234562', 'name' => 'Aisyah Putri Rahmadani', 'gender' => 'P', 'place' => 'Bandar Lampung', 'dob' => '2008-05-18', 'class_id' => $class1->id],
            ['nis' => '240103', 'nisn' => '0071234563', 'name' => 'Bagus Satria Wijaya', 'gender' => 'L', 'place' => 'Natar', 'dob' => '2008-01-25', 'class_id' => $class1->id],
            ['nis' => '240104', 'nisn' => '0071234564', 'name' => 'Dewi Anggraini', 'gender' => 'P', 'place' => 'Pesawaran', 'dob' => '2008-09-09', 'class_id' => $class1->id],
            ['nis' => '240105', 'nisn' => '0071234565', 'name' => 'Dimas Arya Pratama', 'gender' => 'L', 'place' => 'Branti', 'dob' => '2008-07-04', 'class_id' => $class1->id],
            ['nis' => '240106', 'nisn' => '0071234566', 'name' => 'Fitri Nur Halizah', 'gender' => 'P', 'place' => 'Metro', 'dob' => '2008-11-20', 'class_id' => $class1->id],
            ['nis' => '240107', 'nisn' => '0071234567', 'name' => 'Hafidz Al-Ghifari', 'gender' => 'L', 'place' => 'Natar', 'dob' => '2008-04-14', 'class_id' => $class1->id],

            // Kelas XI TKR 1
            ['nis' => '230201', 'nisn' => '0061234571', 'name' => 'Ilham Maulana Yusuf', 'gender' => 'L', 'place' => 'Bandar Lampung', 'dob' => '2007-02-15', 'class_id' => $class2->id],
            ['nis' => '230202', 'nisn' => '0061234572', 'name' => 'Kurniawan Dwi Saputra', 'gender' => 'L', 'place' => 'Lampung Tengah', 'dob' => '2007-08-30', 'class_id' => $class2->id],
            ['nis' => '230203', 'nisn' => '0061234573', 'name' => 'Laila Rohimah', 'gender' => 'P', 'place' => 'Natar', 'dob' => '2007-10-12', 'class_id' => $class2->id],
            ['nis' => '230204', 'nisn' => '0061234574', 'name' => 'Muhammad Rizky Ramadhan', 'gender' => 'L', 'place' => 'Kotabumi', 'dob' => '2007-06-22', 'class_id' => $class2->id],
            ['nis' => '230205', 'nisn' => '0061234575', 'name' => 'Nabila Khairunnisa', 'gender' => 'P', 'place' => 'Lampung Selatan', 'dob' => '2007-12-05', 'class_id' => $class2->id],
            ['nis' => '230206', 'nisn' => '0061234576', 'name' => 'Prasetyo Wibowo', 'gender' => 'L', 'place' => 'Branti', 'dob' => '2007-03-08', 'class_id' => $class2->id],
            ['nis' => '230207', 'nisn' => '0061234577', 'name' => 'Randi Kurnia', 'gender' => 'L', 'place' => 'Natar', 'dob' => '2007-07-19', 'class_id' => $class2->id],

            // Kelas XII AK 1
            ['nis' => '220301', 'nisn' => '0051234581', 'name' => 'Rian Hidayat', 'gender' => 'L', 'place' => 'Bandar Lampung', 'dob' => '2006-05-11', 'class_id' => $class3->id],
            ['nis' => '220302', 'nisn' => '0051234582', 'name' => 'Siti Nur Aini', 'gender' => 'P', 'place' => 'Natar', 'dob' => '2006-01-17', 'class_id' => $class3->id],
            ['nis' => '220303', 'nisn' => '0051234583', 'name' => 'Tri Wahyuni', 'gender' => 'P', 'place' => 'Pesawaran', 'dob' => '2006-09-28', 'class_id' => $class3->id],
            ['nis' => '220304', 'nisn' => '0051234584', 'name' => 'Wahyu Hidayatullah', 'gender' => 'L', 'place' => 'Pringsewu', 'dob' => '2006-04-03', 'class_id' => $class3->id],
            ['nis' => '220305', 'nisn' => '0051234585', 'name' => 'Zahra Amelia', 'gender' => 'P', 'place' => 'Lampung Selatan', 'dob' => '2006-11-14', 'class_id' => $class3->id],
            ['nis' => '220306', 'nisn' => '0051234586', 'name' => 'Zulkifli Anwar', 'gender' => 'L', 'place' => 'Natar', 'dob' => '2006-08-21', 'class_id' => $class3->id],
        ];

        $students = [];
        foreach ($studentsData as $item) {
            $enrollment = '20' . substr($item['nis'], 0, 2);
            $student = Student::firstOrCreate(
                ['nis' => $item['nis']],
                [
                    'school_class_id' => $item['class_id'],
                    'nisn' => $item['nisn'],
                    'name' => $item['name'],
                    'gender' => $item['gender'],
                    'birth_place' => $item['place'],
                    'birth_date' => $item['dob'],
                    'address' => 'Desa Bumisari, Kec. Natar, Kab. Lampung Selatan',
                    'phone' => '0812' . rand(10000000, 99999999),
                    'enrollment_year' => (int) $enrollment,
                    'status' => StudentStatus::ACTIVE,
                ]
            );

            // Generate active QR Code token
            StudentQRCode::firstOrCreate(
                ['student_id' => $student->id, 'is_active' => true],
                [
                    'token' => 'SMKTS-' . (string) Str::uuid() . '-' . Str::random(8),
                    'generated_at' => now(),
                ]
            );

            $students[] = $student;
        }

        // 9. Demo Attendances (Today and past 3 days)
        $today = Carbon::now('Asia/Jakarta');
        for ($i = 0; $i <= 3; $i++) {
            $date = $today->copy()->subDays($i);
            // Skip Sunday
            if ($date->isSunday()) continue;

            $dateStr = $date->toDateString();

            // First 12 students present on time
            for ($s = 0; $s < 12; $s++) {
                $time = sprintf('06:%02d:%02d', rand(15, 55), rand(10, 50));
                Attendance::firstOrCreate(
                    ['student_id' => $students[$s]->id, 'attendance_date' => $dateStr],
                    [
                        'academic_year_id' => $academicYear->id,
                        'semester_id' => $semesterGanjil->id,
                        'check_in_time' => $time,
                        'status' => AttendanceStatus::HADIR,
                        'method' => AttendanceMethod::QR_CODE,
                        'recorded_by' => $admin->id,
                    ]
                );
            }

            // Next 4 students late
            for ($s = 12; $s < 16; $s++) {
                $time = sprintf('07:%02d:%02d', rand(5, 30), rand(10, 50));
                Attendance::firstOrCreate(
                    ['student_id' => $students[$s]->id, 'attendance_date' => $dateStr],
                    [
                        'academic_year_id' => $academicYear->id,
                        'semester_id' => $semesterGanjil->id,
                        'check_in_time' => $time,
                        'status' => AttendanceStatus::TERLAMBAT,
                        'method' => AttendanceMethod::QR_CODE,
                        'recorded_by' => $admin->id,
                    ]
                );
            }
        }

        // 10. Demo Poin Transaksi & Prestasi
        // Top performer: Aisyah Putri Rahmadani (Juara Karakter candidate: high prestasi, 0 moderate/heavy/fatal)
        $aisyah = $students[1];
        PointTransaction::firstOrCreate(
            ['student_id' => $aisyah->id, 'description' => 'Juara 1 LKS Tingkat Provinsi Bidang Web Technologies'],
            [
                'point_category_id' => $createdCategories['Prestasi Lomba / Kelas']->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semesterGanjil->id,
                'type' => PointType::PRESTASI,
                'severity' => null,
                'points' => 75,
                'incident_date' => $today->copy()->subDays(5)->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );
        PointTransaction::firstOrCreate(
            ['student_id' => $aisyah->id, 'description' => 'Inisiatif mengembalikan dompet guru yang tertinggal di lab'],
            [
                'point_category_id' => $createdCategories['Jujur & Beradab']->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semesterGanjil->id,
                'type' => PointType::PRESTASI,
                'severity' => null,
                'points' => 20,
                'incident_date' => $today->copy()->subDays(2)->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );

        // Bagus Satria Wijaya: Prestasi and Minor violation
        $bagus = $students[2];
        PointTransaction::firstOrCreate(
            ['student_id' => $bagus->id, 'description' => 'Pengurus OSIS teladan dan aktif memimpin kegiatan amal sholeh'],
            [
                'point_category_id' => $createdCategories['Tanggung Jawab']->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semesterGanjil->id,
                'type' => PointType::PRESTASI,
                'severity' => null,
                'points' => 30,
                'incident_date' => $today->copy()->subDays(10)->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );
        $bagusPelanggaran = PointTransaction::firstOrCreate(
            ['student_id' => $bagus->id, 'description' => 'Terlambat masuk gerbang sekolah 10 menit'],
            [
                'point_category_id' => $createdCategories['Ringan - Kedisiplinan Umum']->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semesterGanjil->id,
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::RINGAN,
                'points' => -5,
                'incident_date' => $today->copy()->subDays(4)->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );

        // Remedial demo for Bagus
        RemedialPoint::firstOrCreate(
            ['point_transaction_id' => $bagusPelanggaran->id],
            [
                'student_id' => $bagus->id,
                'activity_type' => 'Kerja Bakti Sosial',
                'activity_date' => $today->copy()->subDays(1)->toDateString(),
                'points_deducted' => 5,
                'description' => 'Membersihkan mushola sekolah dan perpustakaan setelah jam pulang',
                'verification_notes' => 'Tugas telah diverifikasi selesai dengan sangat baik oleh guru piket.',
                'recorded_by' => $admin->id,
            ]
        );

        // Demo Sedang violation with Follow Up: Dimas Arya Pratama
        $dimas = $students[4];
        $dimasPelanggaran = PointTransaction::firstOrCreate(
            ['student_id' => $dimas->id, 'description' => 'Membawa HP tanpa izin saat jam pelajaran matematika berlangsung'],
            [
                'point_category_id' => $createdCategories['Pelanggaran Sedang']->id,
                'academic_year_id' => $academicYear->id,
                'semester_id' => $semesterGanjil->id,
                'type' => PointType::PELANGGARAN,
                'severity' => ViolationSeverity::SEDANG,
                'points' => -15,
                'incident_date' => $today->copy()->subDays(6)->toDateString(),
                'recorded_by' => $admin->id,
            ]
        );

        ViolationFollowUp::firstOrCreate(
            ['point_transaction_id' => $dimasPelanggaran->id],
            [
                'student_id' => $dimas->id,
                'follow_up_type' => 'Konseling & Surat Pernyataan',
                'follow_up_date' => $today->copy()->subDays(5)->toDateString(),
                'status' => FollowUpStatus::DALAM_PROSES,
                'counseling_notes' => 'Siswa telah dipanggil ke ruang BK, diberikan pengarahan mengenai peraturan penggunaan gawai di sekolah.',
                'handled_by' => $admin->id,
            ]
        );
    }
}
