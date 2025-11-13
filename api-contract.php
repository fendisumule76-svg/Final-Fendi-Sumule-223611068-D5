<?php
$api_contract = [

    [
    "endpoint" => "/api/v1/health",
    "method" => "GET",
    "description" => "Memeriksa apakah API aktif dan berfungsi dengan baik",
    "response" => [
        "status" => "active",
        "uptime" => "string",
        "timestamp" => "datetime"
    ],
    "status_code" => 200,
    "version" => "v1"
],

    [
        "endpoint" => "/api/v1/auth/login",
        "method" => "POST",
        "description" => "Autentikasi user menggunakan email dan password",
        "request_body" => [
            "email" => "string",
            "password" => "string"
        ],
        "response" => [
            "status" => "success",
            "token" => "string"
        ],
        "status_code" => 200,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/users",
        "method" => "GET",
        "description" => "Mengambil daftar semua pengguna yang terdaftar",
        "response" => [
            "status" => "success",
            "data" => [
                [
                    "id" => "integer",
                    "name" => "string",
                    "email" => "string"
                ]
            ]
        ],
        "status_code" => 200,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/users/{id}",
        "method" => "GET",
        "description" => "Mengambil detail pengguna berdasarkan ID",
        "response" => [
            "status" => "success",
            "data" => [
                "id" => "integer",
                "name" => "string",
                "email" => "string"
            ]
        ],
        "status_code" => 200,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/users",
        "method" => "POST",
        "description" => "Menambahkan pengguna baru",
        "request_body" => [
            "name" => "string",
            "email" => "string",
            "password" => "string"
        ],
        "response" => [
            "status" => "success",
            "message" => "User created successfully",
            "data" => [
                "id" => "integer",
                "name" => "string",
                "email" => "string"
            ]
        ],
        "status_code" => 201,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/users/{id}",
        "method" => "PUT",
        "description" => "Memperbarui data pengguna berdasarkan ID",
        "request_body" => [
            "name" => "string (optional)",
            "email" => "string (optional)",
            "password" => "string (optional)"
        ],
        "response" => [
            "status" => "success",
            "message" => "User updated successfully"
        ],
        "status_code" => 200,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/users/{id}",
        "method" => "DELETE",
        "description" => "Menghapus pengguna berdasarkan ID",
        "response" => [
            "status" => "success",
            "message" => "User deleted successfully"
        ],
        "status_code" => 200,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/upload",
        "method" => "POST",
        "description" => "Mengunggah file ke server",
        "request_body" => [
            "file" => "binary"
        ],
        "response" => [
            "status" => "success",
            "message" => "File uploaded successfully",
            "file_url" => "string"
        ],
        "status_code" => 200,
        "version" => "v1"
    ],
    [
        "endpoint" => "/api/v1/version",
        "method" => "GET",
        "description" => "Menampilkan versi API saat ini",
        "response" => [
            "status" => "success",
            "version" => "v1.0.0"
        ],
        "status_code" => 200,
        "version" => "v1"
    ]
];

header('Content-Type: application/json');
echo json_encode($api_contract, JSON_PRETTY_PRINT);