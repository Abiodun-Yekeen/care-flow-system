<?php
return [

            'patients' => ['view', 'create', 'update'],
            'encounters' => ['view', 'create', 'update', 'close'],
            'observations' => ['view', 'create'],
            'diagnoses' => ['view', 'create'],
            'clinical_notes' => ['view', 'create', 'update'],
            'medications' => ['view'],
            'prescriptions' => ['view', 'create'],
            'dispenses' => ['view', 'dispense'],
            'orders' => ['view', 'create'],
            'lab_tests' => ['view', 'create'],
            'lab_results' => ['view', 'validate'],
            'radiology_reports' => ['view', 'create'],
            'billing' => ['view'],
            'payments' => ['view', 'create'],
            'staff' => ['view','create','update'],
            'department' => ['view', 'create', 'update'],
            'users' => ['view'],
            'audit_logs' => ['view'],
];
