<?php

return [
    'prospect1' => [
        'id' => 1,
        'first_name' => 'Best',
        'last_name' => 'Therapist',
        'email' => 'aaaa@aa.am',
        'service_id' => 1,
        'phone_number' => '+18889991233',
        'address' => 'Test Address 123',
        'city' => 'Test City',
        'state' => 'CA',
        'zip_code' => '99995',
        'country' => 'USA',
        'license_type' => 'RPT',
        'license_number' => 'LL123456789',
        'license_expiration_date' => date_create()->add(new DateInterval('P1Y'))->format('Y-m-d'),
        'language' => ['hy', 'ru'],
		'covered_county' => ['06037', '06059'],
		'covered_city' => ['1840019243', '1840020482', '1840020483', '1840021873'],
		'ip_address' => '192.168.50.1',
        'timezone' => 'Pacific/Guadalcanal',
        'lat' => 18.559008,
        'lng' => 177.20156,
		'note' => 'I do not work in the bad areas!',
        'accepted_by' => null,
        'accepted_at' => null,
        'rejected_by' => null,
        'rejected_at' => null,
        'rejection_reason' => null,
        'status' => 'P',
		'created_by' => '1',
		'created_at' => date_create()->format('Y-m-d H:i:s'),
		'updated_by' => '2',
		'updated_at' => date_create()->format('Y-m-d H:i:s')
	],
    'prospect2' => [
        'id' => 2,
        'first_name' => 'Better',
        'last_name' => 'Therapist',
        'email' => 'bbbb@aa.am',
        'service_id' => 2,
        'phone_number' => '+18889993333',
        'address' => 'Test Address 222',
        'city' => 'Better City',
        'state' => 'CA',
        'zip_code' => '99993',
        'country' => 'USA',
        'license_type' => 'RPT',
        'license_number' => 'DD123456789',
        'license_expiration_date' => date_create()->add(new DateInterval('P6M'))->format('Y-m-d'),
        'language' => ['hy', 'as', 'ru'],
		'covered_county' => ['06037', '06059'],
		'covered_city' => ['1840019243', '1840020482', '1840020483', '1840021873'],
		'ip_address' => '192.168.50.2',
        'timezone' => 'Pacific/Guadalcanal',
        'lat' => -12.171439,
        'lng' => -146.30067,
		'note' => 'I do not work in the good areas!',
		'accepted_by' => null,
		'accepted_at' => null,
		'rejected_by' => null,
		'rejected_at' => null,
		'rejection_reason' => null,
		'status' => 'P',
		'created_by' => 1,
		'created_at' => date_create()->format('Y-m-d H:i:s'),
		'updated_by' => 1,
		'updated_at' => date_create()->format('Y-m-d H:i:s')
	],
    'prospect3' => [
        'id' => 3,
        'first_name' => 'Worst',
        'last_name' => 'Therapist',
        'email' => 'cccc@aa.am',
        'service_id' => 3,
        'phone_number' => '+18889991266',
        'address' => 'Test Address 666',
        'city' => 'Bad City',
        'state' => 'CA',
        'zip_code' => '99996',
        'country' => 'USA',
        'license_type' => 'PTA',
        'license_number' => 'PP123456789',
        'license_expiration_date' => date_create()->add(new DateInterval('P1M'))->format('Y-m-d'),
        'language' => ['hy', 'as', 'az', 'ru'],
        'covered_county' => ['06037', '06059'],
        'covered_city' => ['1840019243', '1840020482', '1840020483', '1840021873'],
        'ip_address' => '192.168.50.3',
        'timezone' => 'Pacific/Guadalcanal',
        'lat' => 85.126285,
        'lng' => 29.724346,
        'note' => 'I do not work any areas!',
        'accepted_by' => null,
        'accepted_at' => null,
        'rejected_by' => 2,
        'rejected_at' => date_create()->format('Y-m-d H:i:s'),
        'rejection_reason' => 'note_documented',
        'status' => 'R',
        'created_by' => 1,
        'created_at' => date_create()->format('Y-m-d H:i:s'),
        'updated_by' => 2,
        'updated_at' => date_create()->format('Y-m-d H:i:s')
    ],
];