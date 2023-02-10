<?php

return [
    'patient1' => [
        'id' => '1',
        'customer_id' => '4',
        'patient_number' => '111',
        'start_of_care' => date_create()->format('Y-m-d'),
        'first_name' => 'Genadi',
        'middle_name' => 'Mini',
        'last_name' => 'Lomita',
        'gender' => 'M',
        'address' => '11 Torrance blvd',
        'city' => 'Torrance',
        'state' => 'CA',
        'country' => 'USA',
        'zip_code' => '90502',
        'birth_date' => '1950-12-12',
        'ssn' => null,
        'phone_number' => null,
        'preferred_language' => '',
        'preferred_gender' => '',
        'emergency_contact_name' => 'My best Friend',
        'emergency_contact_number' => '+18889991234',
        'emergency_contact_relationship' => 'friend',
        'status' => 'A',
        'created_by' => '4',
        'created_at' => date_create()->format('Y-m-d H:i:s'),
        'updated_by' => '2',
        'updated_at' => date_create()->format('Y-m-d H:i:s')
    ],
    'patient2' => [
        'id' => '2',
        'customer_id' => '4',
        'patient_number' => '222',
        'start_of_care' => date_create()->format('Y-m-d'),
        'first_name' => 'Angela',
        'middle_name' => 'Stop',
        'last_name' => 'Alvarez',
        'gender' => 'F',
        'address' => '22 Glendale blvd',
        'city' => 'Glendale',
        'state' => 'CA',
        'country' => 'USA',
        'zip_code' => '90102',
        'birth_date' => '1950-12-10',
        'ssn' => null,
        'phone_number' => null,
        'preferred_language' => '',
        'preferred_gender' => '',
        'emergency_contact_name' => 'My best son',
        'emergency_contact_number' => '+18889992222',
        'emergency_contact_relationship' => 'son',
        'status' => 'A',
        'created_by' => '4',
        'created_at' => date_create()->format('Y-m-d H:i:s'),
        'updated_by' => '2',
        'updated_at' => date_create()->format('Y-m-d H:i:s')
    ]
];