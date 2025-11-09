<?php
require 'Conn.php'; // make sure this creates $conn (mysqli)

$error_message = '';
$success_message = '';

$username = $fullname = $nric = $age = $dob = $gender = $pwd_status = '';
$moNumber = $email = $confirm_email = $add1 = $add2 = $city = $state = $postcode = '';
$licenseType = $expiry_date = '';
$agreed_terms = $agreed_privacy = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read POST data
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $fullname = trim($_POST['name'] ?? '');
    $nric = trim($_POST['nric'] ?? '');
    $age = $_POST['Age'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $pwd_status = $_POST['pwd'] ?? '';
    $moNumber = trim($_POST['moNumber'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $confirm_email = trim($_POST['confirm_email'] ?? '');
    $add1 = trim($_POST['Add1'] ?? '');
    $add2 = trim($_POST['Add2'] ?? '');
    $city = trim($_POST['City'] ?? '');
    $state = $_POST['State'] ?? '';
    $postcode = trim($_POST['Postcode'] ?? '');
    $licenseType = $_POST['LicenseType'] ?? '';
    $expiry_date = $_POST['expiry_date'] ?? '';
    $photo = $_FILES['photo'] ?? null;

    $agreed_terms = isset($_POST['terms_conditions']);
    $agreed_privacy = isset($_POST['privacy_policy']);

    // Validation
    if ($username === '' || $password === '' || $confirm === '') {
        $error_message = 'Username and password are required.';
    } elseif ($password !== $confirm) {
        $error_message = 'Passwords do not match.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email.';
    } elseif ($email !== $confirm_email) {
        $error_message = 'Email addresses do not match.';
    } elseif (!$agreed_terms || !$agreed_privacy) {
        $error_message = 'You must agree to terms and privacy policy.';
    } else {
        // Check username
        $check = $conn->prepare("SELECT id FROM users WHERE username = ?");
        if ($check === false) {
            $error_message = "Database error: " . $conn->error;
        } else {
            $check->bind_param("s", $username);
            $check->execute();
            $res = $check->get_result();

            if ($res && $res->num_rows > 0) {
                $error_message = 'Username already taken.';
                $check->close();
            } else {
                $check->close();

                // Handle file upload
                $photo_path = null;
                if ($photo && isset($photo['tmp_name']) && $photo['tmp_name'] !== '') {
                    $upload_dir = 'uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    $photo_path = $upload_dir . time() . '_' . basename($photo['name']);
                    move_uploaded_file($photo['tmp_name'], $photo_path);
                }

                // Insert user
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO users (username,password,fullname,nric,age,dob,gender,pwd_status,moNumber,email,address1,address2,city,state,postcode,licenseType,expiry_date,photo_path)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    $error_message = "Database error: " . $conn->error;
                } else {
                    $stmt->bind_param(
                        "ssssssssssssssssss",
                        $username, $hashed, $fullname, $nric, $age, $dob, $gender, $pwd_status,
                        $moNumber, $email, $add1, $add2, $city, $state, $postcode,
                        $licenseType, $expiry_date, $photo_path
                    );

                    if ($stmt->execute()) {
                        $success_message = "Registered successfully. Redirecting to login...";
                        header("refresh:2;url=Login.php");
                    } else {
                        $error_message = "Registration failed: " . $stmt->error;
                    }
                    $stmt->close();
                }
            }
        }
    }
}
?>

<html>
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Capydeng Car Rental - Signup</title>
<style>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap');

* { box-sizing:border-box; margin:0; padding:0; font-family:'Poppins', sans-serif; }

body {
    background: linear-gradient(135deg,#a8c0ff,#3f2b96);
    min-height:100vh;
    display:flex;
    justify-content:center;
    align-items:flex-start;
    padding:20px;
}

.signup-card {
    background:white;
    border-radius:20px;
    max-width:900px;
    width:100%;
    padding:35px 40px;
    box-shadow:0 12px 30px rgba(0,0,0,0.2);
    margin-top:30px;
    overflow:hidden;
}

.signup-card h2 {
    text-align:center;
    color:#3f2b96;
    margin-bottom:25px;
    font-weight:700;
    font-size:28px;
}

fieldset {
    border:none;
    padding:20px 15px;
    margin-bottom:25px;
    border-radius:12px;
    background: #f0f2ff;
}

legend {
    font-size:1.1em;
    font-weight:600;
    color:#3f2b96;
    margin-bottom:15px;
    border-bottom:2px solid #3f2b96;
    display:inline-block;
    padding-bottom:3px;
}

label {
    display:block;
    font-weight:500;
    margin-bottom:6px;
    color:#333;
}

input, select {
    width:100%;
    padding:10px 12px;
    border-radius:8px;
    border:1px solid #ccc;
    font-size:14px;
    margin-bottom:15px;
    transition:0.3s;
}

input:focus, select:focus {
    outline:none;
    border-color:#ff8c00;
    box-shadow:0 0 6px rgba(255,140,0,0.3);
}

.row {
    display:flex;
    flex-wrap:wrap;
    gap:15px;
}

.col { flex:1; min-width:220px; }

.buttons {
    display:flex;
    justify-content:center;
    gap:15px;
    margin-top:20px;
}

.btn-primary, .btn-reset {
    padding:12px 25px;
    border:none;
    border-radius:10px;
    cursor:pointer;
    font-weight:600;
    font-size:16px;
    transition:0.3s;
}

.btn-primary { background:linear-gradient(90deg,#ff8c00,#ff6600); color:white; }
.btn-primary:hover { background:linear-gradient(90deg,#ff6600,#ff4500); transform:scale(1.03); }

.btn-reset { background:#6c757d; color:white; }
.btn-reset:hover { background:#868e96; transform:scale(1.03); }

.msg { padding:12px; border-radius:10px; margin-bottom:20px; font-weight:500; text-align:center; }
.error { background:#ffe2e2; color:#b30000; }
.success { background:#d4edda; color:#155724; }

.small {
    text-align:center;
    font-size:0.9em;
    color:#555;
    margin-top:15px;
}

@media(max-width:768px){
    .row{flex-direction:column;}
    .signup-card{padding:25px 20px;}
}
</style>
</head>
<body>

<div class="signup-card">
<h2>Register Your Account</h2>

<?php if ($error_message): ?>
    <div class="msg error"><?php echo htmlspecialchars($error_message); ?></div>
<?php endif; ?>
<?php if ($success_message): ?>
    <div class="msg success"><?php echo htmlspecialchars($success_message); ?></div>
<?php endif; ?>

<form method="post" action="" enctype="multipart/form-data">

    <!-- Account Details -->
    <fieldset>
        <legend>Account Details</legend>
        <div class="row">
            <div class="col">
                <label>Username *</label>
                <input type="text" name="username" required value="<?php echo htmlspecialchars($username ?? ''); ?>">
            </div>
            <div class="col">
                <label>Password *</label>
                <input type="password" name="password" required>
            </div>
            <div class="col">
                <label>Confirm Password *</label>
                <input type="password" name="confirm_password" required>
            </div>
        </div>
    </fieldset>
    
    <!-- Personal Details -->
    <fieldset>
        <legend>Personal Details</legend>
        <div class="row">
            <div class="col">
                <label>Full Name *</label>
                <input type="text" name="name" required value="<?php echo htmlspecialchars($fullname ?? ''); ?>">
            </div>
            <div class="col">
                <label>MyKad / NRIC *</label>
                <input type="text" name="nric" required value="<?php echo htmlspecialchars($nric ?? ''); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Age *</label>
                <select name="Age" required>
                    <option value="">-Select-</option>
                    <?php
                    $ages = ['18-24','25-34','35-44','45-54','55-60'];
                    foreach($ages as $opt){
                        echo "<option".(($age==$opt)?' selected':'').">$opt</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="col">
                <label>Date of Birth *</label>
                <input type="date" name="dob" required value="<?php echo htmlspecialchars($dob ?? ''); ?>">
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Gender *</label>
                <select name="gender" required>
                    <option value="">-Select-</option>
                    <option value="male" <?php if($gender=='male') echo 'selected'; ?>>Male</option>
                    <option value="female" <?php if($gender=='female') echo 'selected'; ?>>Female</option>
                </select>
            </div>
            <div class="col">
                <label>Person with disabilities *</label>
                <select name="pwd" required>
                    <option value="">-Select-</option>
                    <option value="No" <?php if($pwd_status=='No') echo 'selected'; ?>>No</option>
                    <option value="Yes" <?php if($pwd_status=='Yes') echo 'selected'; ?>>Yes</option>
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <label>Mobile Number *</label>
                <input type="text" name="moNumber" required value="<?php echo htmlspecialchars($moNumber ?? ''); ?>">
            </div>
            <div class="col">
                <label>Email *</label>
                <input type="email" name="email" required value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
        </div>
        <div class="field">
            <label>Confirm Email *</label>
            <input type="email" name="confirm_email" required value="<?php echo htmlspecialchars($confirm_email ?? ''); ?>">
        </div>
        <div class="field">
            <label>Address Line 1 *</label>
            <input type="text" name="Add1" required value="<?php echo htmlspecialchars($add1 ?? ''); ?>">
        </div>
        <div class="field">
            <label>Address Line 2</label>
            <input type="text" name="Add2" value="<?php echo htmlspecialchars($add2 ?? ''); ?>">
        </div>
        <div class="row">
            <div class="col">
                <label>City *</label>
                <input type="text" name="City" required value="<?php echo htmlspecialchars($city ?? ''); ?>">
            </div>
            <div class="col">
                <label>State *</label>
                <select name="State" required>
                    <option value="">-Select-</option>
                    <?php
                    $states = ['Johor','Kedah','Kelantan','Melaka','Negeri Sembilan','Pahang','Perak','Perlis','Penang','Selangor','Terengganu','Wilayah Persekutuan'];
                    foreach($states as $opt){
                        echo "<option".(($state==$opt)?' selected':'').">$opt</option>";
                    }
                    ?>
                </select>
            </div>
        </div>
        <div class="field">
            <label>Postcode *</label>
            <input type="text" name="Postcode" required value="<?php echo htmlspecialchars($postcode ?? ''); ?>">
        </div>
    </fieldset>

    <!-- Driver's License -->
    <fieldset>
        <legend>Driver's License</legend>
        <div class="row">
            <div class="col">
                <label>License Type *</label>
                <select name="LicenseType" required>
                    <option value="">-Select-</option>
                    <option value="CDL" <?php if($licenseType=='CDL') echo 'selected'; ?>>CDL</option>
                    <option value="P License" <?php if($licenseType=='P License') echo 'selected'; ?>>P License</option>
                </select>
            </div>
            <div class="col">
                <label>Expiry Date *</label>
                <input type="date" name="expiry_date" required value="<?php echo htmlspecialchars($expiry_date ?? ''); ?>">
            </div>
        </div>
        <div class="field">
            <label>Upload License Photo *</label>
            <input type="file" name="photo" accept="image/*">
        </div>
    </fieldset>

    <!-- Terms -->
    <fieldset>
        <label><input type="checkbox" name="terms_conditions" <?php if($agreed_terms) echo 'checked'; ?>> I accept terms & conditions *</label>
        <label><input type="checkbox" name="privacy_policy" <?php if($agreed_privacy) echo 'checked'; ?>> I accept privacy policy *</label>
    </fieldset>

    <div class="buttons">
        <button class="btn-primary" type="submit">Register</button>
        <button class="btn-reset" type="reset">Clear</button>
    </div>
</form>

<p class="small">Already have an account? <a href="Login.php">Login here</a></p>
</div>

</body>
</html>
