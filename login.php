<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Absolute Cinema</title>
  
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">

  <style>
    /* 
      A few custom styles to replicate the dark theme variables 
      (bg-background, bg-card, and bg-input-background) from the original code.
    */
    body {
      background-color: #0f0f0f;
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
<body class="vh-100 d-flex align-items-center justify-content-center px-3">

  
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
        
        <div class="mb-3">
          <label for="name" class="form-label text-white mb-2">Full Name</label>
          <input 
            type="text" 
            id="name" 
            class="form-control py-2 bg-dark text-white border-secondary" 
            placeholder="John Doe" 
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