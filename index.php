<!DOCTYPE html>
<html lang="id">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Jadwal Pelajaran</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

<style>

body{
    margin:0;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:
    linear-gradient(
    135deg,
    #2563eb,
    #1e293b
    );
    font-family:Arial;
}

.main-box{

    background:white;
    padding:50px;
    border-radius:25px;
    width:400px;

    text-align:center;

    box-shadow:
    0 10px 30px rgba(0,0,0,0.2);
}

.main-box h1{

    margin-bottom:15px;
    font-weight:bold;
}

.main-box p{

    color:gray;
    margin-bottom:35px;
}

.btn-custom{

    width:100%;
    padding:14px;
    margin-bottom:15px;

    border:none;
    border-radius:12px;

    font-size:16px;
    font-weight:bold;

    transition:0.3s;
}

.btn-user{

    background:#2563eb;
    color:white;
}

.btn-user:hover{

    background:#1d4ed8;
}

.btn-register{

    background:#10b981;
    color:white;
}

.btn-register:hover{

    background:#059669;
}

.btn-admin{

    background:#111827;
    color:white;
}

.btn-admin:hover{

    background:#000;
}

</style>

</head>

<body>

<div class="main-box">

    <h1>
        Jadwal App
    </h1>

    <p>
        Sistem Informasi Jadwal Pelajaran
    </p>

    <a href="auth/login_user.php">

        <button class="btn-custom btn-user">

            <i class="fa fa-user"></i>

            Login User

        </button>

    </a>

    <a href="auth/register.php">

        <button class="btn-custom btn-register">

            <i class="fa fa-user-plus"></i>

            Register User

        </button>

    </a>

    <a href="auth/login_admin.php">

        <button class="btn-custom btn-admin">

            <i class="fa fa-lock"></i>

            Login Admin

        </button>

    </a>

</div>

</body>
</html>