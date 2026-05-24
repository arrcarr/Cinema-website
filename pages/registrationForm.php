<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Absolute Cinema</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <style>
    /* A few custom styles to replicate the dark theme variables 
    */
    body {
      background-color: #0f0f0f;
      color-scheme: dark; /* Ensures native date picker matches dark mode */
    }
    .custom-card {
      background-color: #1a1a1a;
      border: 1px solid #2d2d2d;
    }
    .custom-input {
      background-color: #242424;
      border: 1px solid #2d2d2d;
      color: white;
      padding-left: 2.5rem; /* Space for the absolute positioned icons */
    }
    .custom-input:focus {
      background-color: #242424;
      color: white;
      border-color: #dc3545; /* Bootstrap danger red */
      box-shadow: none;
    }
    .custom-input::placeholder {
      color: #6c757d;
    }
    .input-icon {
      position: absolute;
      left: 1rem;
      top: 50%;
      transform: translateY(-50%);
      color: #6c757d;
    }
  </style>
</head>
<body class="min-vh-100 d-flex align-items-center justify-content-center px-3 py-5">

  <div class="w-100" style="max-width: 28rem;">
    
    <div class="d-flex align-items-center justify-content-center gap-2 mb-5">
      <i class="bi bi-film text-danger fs-1"></i>
      <span class="fs-2 fw-bold text-white">
        ABSOLUTE <span class="text-danger">CINEMA</span>
      </span>
    </div>

    <div class="custom-card rounded-3 p-4 p-md-5">
      <h2 class="fs-4 fw-bold text-white mb-4 text-center">
        Create Account
      </h2>

      <form>
        
        <div class="row g-3 mb-3">
          <div class="col-sm-6">
            <label for="firstName" class="form-label text-white mb-2">First Name</label>
            <input 
              type="text" 
              id="firstName" 
              class="form-control py-2 bg-dark text-white border-secondary" 
              placeholder="John" 
              required>
          </div>
          <div class="col-sm-6">
            <label for="lastName" class="form-label text-white mb-2">Last Name</label>
            <input 
              type="text" 
              id="lastName" 
              class="form-control py-2 bg-dark text-white border-secondary" 
              placeholder="Doe" 
              required>
          </div>
        </div>

        <div class="mb-3">
          <label for="birthday" class="form-label text-white mb-2">Birthday</label>
          <input 
            type="date" 
            id="birthday" 
            class="form-control py-2 bg-dark text-white border-secondary" 
            required>
        </div>

        <div class="mb-3">
          <label for="profilePic" class="form-label text-white mb-2">Profile Picture</label>
          <input 
            type="file" 
            id="profilePic" 
            class="form-control bg-dark text-white border-secondary" 
            accept="image/*"
            required>
        </div>

        <div class="mb-3">
          <label for="email" class="form-label text-white mb-2">Email</label>
          <div class="position-relative">
            <i class="bi bi-envelope input-icon"></i>
            <input 
              type="email" 
              id="email" 
              class="form-control py-2 custom-input" 
              placeholder="you@example.com" 
              required>
          </div>
        </div>

        <div class="mb-3">
          <label for="password" class="form-label text-white mb-2">Password</label>
          <div class="position-relative">
            <i class="bi bi-lock input-icon"></i>
            <input 
              type="password" 
              id="password" 
              class="form-control py-2 custom-input" 
              placeholder="••••••••" 
              required>
          </div>
        </div>

        <div class="mb-4">
          <label for="confirmPassword" class="form-label text-white mb-2">Confirm Password</label>
          <div class="position-relative">
            <i class="bi bi-lock input-icon"></i>
            <input 
              type="password" 
              id="confirmPassword" 
              class="form-control py-2 custom-input" 
              placeholder="••••••••" 
              required>
          </div>
        </div>

        <button type="submit" class="btn btn-danger w-100 py-2 fw-semibold">
          Register
        </button>
      </form>

      <div class="mt-4 text-center">
        <button class="btn btn-link text-danger text-decoration-none p-0 shadow-none">
          Already have an account? Login
        </button>
      </div>
    </div>
    
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>