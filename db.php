<?php

/* - Database Settings - */
// Railway��ł͊��ϐ�����ڑ������擾�E���[�J��(XAMPP)�ł͊��ϐ����Ȃ����� ?: �̉E���̒l���g�p
define('DB_HOST', getenv('MYSQLHOST')     ?: 'localhost'); // �f�[�^�x�[�X�̏ꏊ
define('DB_PORT', getenv('MYSQLPORT')     ?: '3306');      // �ڑ�����|�[�g�ԍ�
define('DB_NAME', getenv('MYSQLDATABASE') ?: 'cadency');   // �f�[�^�x�[�X��
define('DB_USER', getenv('MYSQLUSER')     ?: 'root');      // ���O�C�����[�U�[��
define('DB_PASS', getenv('MYSQLPASSWORD') ?: '');          // ���O�C���p�X���[�h


/* - Database Connection - */
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,  // �G���[���ɗ�O�𓊂���
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,        // ���ʂ�A�z�z��Ŏ擾
            PDO::ATTR_EMULATE_PREPARES   => false,                   // SQL���̈��S�`�F�b�N��MySQL�{�̂ɂ���Ă��炤(�����S)
        ]
    );
} catch (PDOException $e) {
http_response_code(500);
    error_log('DB connection failed: ' . $e->getMessage()); // 【デバッグ用】原因確認後に削除
    exit('DB connection failed.');
}
