<?php
/**
 * create_admin.php (fixed)
 * Ensures role id=1 exists, then creates or updates an Admin user.
 *
 * Edit DB credentials and admin info below before running.
 */

$host = "localhost";
$user = "root";
$pass = "";
$db   = "rms";

// ****** ADMIN DETAILS HERE ********
$adminName   = "Admin";
$adminEmail  = "admin@rms.com";
$adminPass   = "admin@123";   // plain — will be hashed
$desiredRole = 1;             // desired role_id to assign (ensure exists)
$department  = null;          // set integer or null
// **********************************

echo "Connecting to database...\n";

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("❌ Connection failed: " . $conn->connect_error . PHP_EOL);
}
echo "Connected.\n\n";

// 1) Ensure the role (id = $desiredRole) exists. Create / upsert it safely.
$roleId = (int)$desiredRole;
$roleName = 'Admin';
$upsertRoleSql = "INSERT INTO roles (id, name, created_at, updated_at)
                  VALUES ($roleId, ?, NOW(), NOW())
                  ON DUPLICATE KEY UPDATE name = VALUES(name), updated_at = NOW()";
if (!($stmt = $conn->prepare($upsertRoleSql))) {
    die("Prepare failed for role upsert: " . $conn->error . PHP_EOL);
}
$stmt->bind_param("s", $roleName);
if (!$stmt->execute()) {
    die("Failed to ensure role exists: " . $stmt->error . PHP_EOL);
}
$stmt->close();
echo "✅ Ensured role id={$roleId} exists (name='{$roleName}').\n";

// 2) Hash the password
$hashed = password_hash($adminPass, PASSWORD_DEFAULT);
echo "Generated password hash: $hashed\n\n";

// 3) Check if user exists
$check = $conn->prepare("SELECT id FROM users WHERE email = ?");
if (!$check) {
    die("Prepare failed (select user): " . $conn->error . PHP_EOL);
}
$check->bind_param("s", $adminEmail);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->bind_result($existingId);
    $check->fetch();
    echo "⚠️ User already exists with ID: {$existingId} — updating password, role and status...\n";

    // Build update SQL depending on whether department is NULL
    if ($department === null) {
        $updateSql = "UPDATE users
                      SET password_hash = ?, role_id = ?, department_id = NULL, status = 1, updated_at = NOW()
                      WHERE id = ?";
        $upd = $conn->prepare($updateSql);
        if (!$upd) die("Prepare failed (update): " . $conn->error . PHP_EOL);
        $upd->bind_param("sii", $hashed, $roleId, $existingId);
    } else {
        $department = (int)$department;
        $updateSql = "UPDATE users
                      SET password_hash = ?, role_id = ?, department_id = ?, status = 1, updated_at = NOW()
                      WHERE id = ?";
        $upd = $conn->prepare($updateSql);
        if (!$upd) die("Prepare failed (update): " . $conn->error . PHP_EOL);
        $upd->bind_param("siii", $hashed, $roleId, $department, $existingId);
    }

    if (!$upd->execute()) {
        die("❌ Failed to update admin: " . $upd->error . PHP_EOL);
    }
    $upd->close();
    echo "✅ Admin updated (ID: {$existingId}).\n";
    exit;
}
$check->close();

// 4) Insert new user (handle department NULL)
if ($department === null) {
    $insertSql = "INSERT INTO users (name, email, password_hash, role_id, department_id, status, created_at)
                  VALUES (?, ?, ?, ?, NULL, 1, NOW())";
    $ins = $conn->prepare($insertSql);
    if (!$ins) die("Prepare failed (insert): " . $conn->error . PHP_EOL);
    $ins->bind_param("sssi", $adminName, $adminEmail, $hashed, $roleId);
} else {
    $department = (int)$department;
    $insertSql = "INSERT INTO users (name, email, password_hash, role_id, department_id, status, created_at)
                  VALUES (?, ?, ?, ?, ?, 1, NOW())";
    $ins = $conn->prepare($insertSql);
    if (!$ins) die("Prepare failed (insert): " . $conn->error . PHP_EOL);
    $ins->bind_param("sssis", $adminName, $adminEmail, $hashed, $roleId, $department);
}

if (!$ins->execute()) {
    die("❌ Failed to insert admin: " . $ins->error . PHP_EOL);
}
$adminId = $ins->insert_id;
$ins->close();

echo "✅ Admin user created with ID: {$adminId}\n\n";
echo "🎉 Admin setup complete!\n";
echo "Login with:\n";
echo "Email: {$adminEmail}\n";
echo "Password: {$adminPass}\n";
