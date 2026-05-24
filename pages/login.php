<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Absolute Cinema</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

    <style>
        /* Custom CSS strictly for absolute positioning of the input icons */
        .input-icon-wrapper {
            position: relative;
        }

        .input-icon-wrapper .bi {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            /* Bootstrap's text-secondary */
        }

        .input-icon-wrapper .form-control {
            padding-left: 2.5rem;
            /* Makes room for the icon */
        }

        .input-icon-wrapper .form-control:focus {
            border-color: var(--bs-danger);
            box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
        }
    </style>
</head>

<body class="bg-black text-white d-flex align-items-center justify-content-center min-vh-100 px-3">

    <div class="w-100" style="max-width: 28rem;">

        <div class="d-flex align-items-center justify-content-center gap-2 mb-5">
            <i class="bi bi-film text-danger fs-1"></i>
            <span class="fs-2 fw-bold text-white">
                ABSOLUTE <span class="text-danger">CINEMA</span>
            </span>
        </div>

        <div class="card bg-black border-secondary p-4 p-md-5 rounded-3 shadow">
            <h2 class="fs-4 fw-bold text-white mb-4 text-center">
                Welcome Back
            </h2>

            <form action="#" method="POST">

                <div class="mb-3">
                    <label for="email" class="form-label text-white mb-2 fw-medium">Email</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-envelope"></i>
                        <input type="email" id="email" name="email"
                            class="form-control py-2 bg-black text-white border-secondary" placeholder="you@example.com"
                            required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-white mb-2 fw-medium">Password</label>
                    <div class="input-icon-wrapper">
                        <i class="bi bi-lock"></i>
                        <input type="password" id="password" name="password"
                            class="form-control py-2 bg-black text-white border-secondary" placeholder="••••••••"
                            required>
                    </div>
                </div>

                <button type="submit" class="btn btn-danger w-100 py-2 fw-bold">
                    Login
                </button>
            </form>

            <div class="mt-4 text-center">
                <a href="#" class="text-danger text-decoration-none hover-opacity-75">
                    Don't have an account? <u>Register</u>
                </a>
            </div>
        </div>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>