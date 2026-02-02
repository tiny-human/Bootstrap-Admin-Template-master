<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <!-- SEO Meta Tags -->
    <meta name="description" content="Advanced form examples with real-time validation, file uploads, and multi-step wizards">
    <meta name="keywords" content="bootstrap, admin, dashboard, forms, validation">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="./assets/favicon-CvUZKS4z.svg">
    <link rel="icon" type="image/png" href="./assets/favicon-B_cwPWBd.png">

    <!-- PWA Manifest -->
    <link rel="manifest" href="./assets/manifest-DTaoG9pG.json">

    <!-- Preload critical fonts -->
    <link rel="preload" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script type="module" crossorigin src="./assets/vendor-bootstrap-C9iorZI5.js"></script>
    <script type="module" crossorigin src="./assets/vendor-charts-DGwYAWel.js"></script>
    <script type="module" crossorigin src="./assets/vendor-ui-CflGdlft.js"></script>
    <script type="module" crossorigin src="./assets/main-DwHigVru.js"></script>
    <script type="module" crossorigin src="./assets/forms-CC-rf4V3.js"></script>
    <link rel="stylesheet" crossorigin href="./assets/main-QD_VOj1Y.css">
</head>

<body>
    <div class="container min-vh-100 d-flex justify-content-center align-items-center">
        <div class="col-lg-5 col-md-7">
            <div class="card shadow">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-person-plus me-2 text-success"></i>
                        Auto-login
                    </h5>
                </div>

                <div class="card-body">
                    <form action="/register" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Username</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-person"></i></span>
                                    <input type="text"
                                        class="form-control"
                                        x-model="form.username"
                                        @input="validateField('username')"
                                        :class="getFieldClass('username')"
                                        placeholder="Enter username"
                                        name="nom"
                                        required>
                                </div>
                                <div class="invalid-feedback"
                                    x-show="errors.username"
                                    x-text="errors.username"></div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Email</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                    <input type="email"
                                        class="form-control"
                                        x-model="form.email"
                                        @input="validateField('email')"
                                        :class="getFieldClass('email')"
                                        placeholder="Enter email"
                                        name="email"
                                        required>

                                </div>
                                <div class="invalid-feedback"
                                    x-show="errors.email"
                                    x-text="errors.email"></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Password</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                    <input
                                        :type="showPassword ? 'text' : 'password'"
                                        class="form-control"
                                        x-model="form.password"
                                        @input="validatePassword()"
                                        :class="getFieldClass('password')"
                                        placeholder="Enter password"
                                        name="mdp"
                                        required>
                                </div>
                                <div class="invalid-feedback" x-show="errors.password" x-text="errors.password"></div>

                            </div>
                            <div class="col-md-6"></div>

                            <div class="col-12">
                                <div class="form-check">
                                    <input class="form-check-input"
                                        type="checkbox"
                                        x-model="form.agreeTerms"
                                        required>
                                    <label class="form-check-label">
                                        I agree to the
                                        <a href="#" class="text-primary">Terms of Service</a>
                                        and
                                        <a href="#" class="text-primary">Privacy Policy</a>
                                    </label>
                                </div>
                            </div>

                            <div class="col-12 text-center">
                                <button type="submit" class="btn btn-success w-100">
                                    <i class="bi bi-check-circle me-2"></i>
                                    Valider
                                </button>

                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>



</body>

</html>