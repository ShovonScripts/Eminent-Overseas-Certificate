# 3. Database

## 3.1 ERD

```
students 1 ──< certificates >── 1 courses
                   │
                   ├──< certificate_attendances      (monthly rows)
                   ├──< certificate_instructor >── instructors
                   └──< verification_logs
users 1 ──< certificates (issued_by)
```

## 3.2 Tables

### students
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| student_code | string unique | e.g. `EOC-S-0001` |
| name_en | string | "Md. Anik" |
| name_ja | string null | optional katakana |
| date_of_birth | date | never shown publicly |
| gender | enum(male,female,other) | printed as 男性 / 女性 |
| email, phone | string null | |
| timestamps, softDeletes | | |

### courses
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| name_ja | string | 日本語初級 |
| name_en | string | Basic Level |
| level_code | string | N5 |
| aim_level_ja | string | JLPT N5 修了相当 |
| aim_level_en | string | N5 Level |
| total_hours | unsigned smallint | 180 |
| book_ja / book_en | string | みんなの日本語 / Minna No Nihongo |
| learning_context | json | `[{"ja":"会話","en":"Conversation"}, ...]` |
| default_hours_per_day | tinyint | 3 |
| default_days_per_week | tinyint | 3 |

### instructors
| Column | Type |
|---|---|
| id, name, email, signature_path null, is_active, timestamps | |

### certificates
| Column | Type | Notes |
|---|---|---|
| id | bigint PK | |
| certificate_no | string unique | `EOC-2026-000001` |
| verification_code | char(26) unique | ULID, used in QR URL |
| student_id | FK | |
| course_id | FK | |
| study_start, study_end | date | |
| hours_per_day, days_per_week | tinyint | |
| class_days | json | `["Saturday","Monday","Wednesday"]` |
| national_holidays | string null | "6月9日; 6月11日; 8月16日; 10月1日" |
| total_class_hours | smallint | calculated |
| total_attendance_hours | smallint | calculated |
| attendance_rate | decimal(5,2) | calculated |
| issued_on | date | 発行日 |
| status | enum(draft,issued,revoked) | default draft |
| issued_by | FK users null | |
| issued_at | timestamp null | actual issue time |
| revoked_at, revoke_reason | null | |
| pdf_path | string null | private disk path |
| pdf_sha256 | char(64) null | integrity check |
| timestamps, softDeletes | | |

Indexes: `verification_code` (unique), `certificate_no` (unique), `student_id`, `status`.

### certificate_attendances
| Column | Type |
|---|---|
| id, certificate_id FK (cascade) | |
| year (smallint), month (tinyint) | |
| class_hours, attendance_hours | unsigned smallint |

Unique: `(certificate_id, year, month)`.

### certificate_instructor (pivot)
`certificate_id`, `instructor_id`, `position` (tinyint, 1 = left, 2 = right).

### verification_logs
| Column | Type |
|---|---|
| id | bigint |
| certificate_id | FK null (null when code unknown) |
| code_tried | string(64) |
| result | enum(valid,revoked,not_found) |
| ip_address | string(45) |
| user_agent | string(255) null |
| created_at | timestamp |

## 3.3 Example migration (certificates)

```php
Schema::create('certificates', function (Blueprint $table) {
    $table->id();
    $table->string('certificate_no')->unique();
    $table->char('verification_code', 26)->unique();
    $table->foreignId('student_id')->constrained();
    $table->foreignId('course_id')->constrained();
    $table->date('study_start');
    $table->date('study_end');
    $table->unsignedTinyInteger('hours_per_day');
    $table->unsignedTinyInteger('days_per_week');
    $table->json('class_days');
    $table->string('national_holidays')->nullable();
    $table->unsignedSmallInteger('total_class_hours')->default(0);
    $table->unsignedSmallInteger('total_attendance_hours')->default(0);
    $table->decimal('attendance_rate', 5, 2)->default(0);
    $table->date('issued_on');
    $table->string('status', 10)->default('draft')->index();
    $table->foreignId('issued_by')->nullable()->constrained('users');
    $table->timestamp('issued_at')->nullable();
    $table->timestamp('revoked_at')->nullable();
    $table->string('revoke_reason')->nullable();
    $table->string('pdf_path')->nullable();
    $table->char('pdf_sha256', 64)->nullable();
    $table->timestamps();
    $table->softDeletes();
});
```

## 3.4 Seed data from the current certificate

```php
Course::create([
    'name_ja' => '日本語初級', 'name_en' => 'Basic Level',
    'level_code' => 'N5',
    'aim_level_ja' => 'JLPT N5 修了相当', 'aim_level_en' => 'N5 Level',
    'total_hours' => 180,
    'book_ja' => 'みんなの日本語', 'book_en' => 'Minna No Nihongo',
    'learning_context' => [
        ['ja'=>'会話','en'=>'Conversation'], ['ja'=>'語彙','en'=>'Vocabulary'],
        ['ja'=>'文法','en'=>'Grammar'],      ['ja'=>'読解','en'=>'Reading'],
        ['ja'=>'聴解','en'=>'Listening'],    ['ja'=>'作文','en'=>'Writing'],
    ],
    'default_hours_per_day' => 3, 'default_days_per_week' => 3,
]);

Instructor::insert([
  ['name'=>'Sheikh Monujurl Islam','email'=>'info@eminentoverseas.uk','is_active'=>true],
  ['name'=>'Abdul Kaium Ovi','email'=>'info@eminentoverseas.uk','is_active'=>true],
]);
```

## 3.5 Sample monthly data (from the current certificate)

| Month | Class hr | Attendance hr | Rate |
|---|---|---|---|
| 2025-06 | 33 | 33 | 100% |
| 2025-07 | 39 | 39 | 100% |
| 2025-08 | 36 | 36 | 100% |
| 2025-09 | 39 | 39 | 100% |
| 2025-10 | 33 | 33 | 100% |
| **Total** | **180** | **180** | **100%** |

The system can **auto-suggest** monthly class hours from the start/end dates, class days, hours per day, and the holiday list.
