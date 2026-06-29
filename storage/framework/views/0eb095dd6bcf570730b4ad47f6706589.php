<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?php echo e(\App\Models\Setting::getValue('app_name', config('app.name', 'Church'))); ?></title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    <?php echo app('Illuminate\Foundation\Vite')(['resources/css/app.css', 'resources/js/app.js']); ?>
    <?php
        $sliders = \App\Models\SliderImage::where('is_active', true)->orderBy('order')->get();
        $appName = \App\Models\Setting::getValue('app_name', 'Glory Assembly');
        $tagline = \App\Models\Setting::getValue('tagline', 'A place where lives are transformed');
        $description = \App\Models\Setting::getValue('description');
        $address = \App\Models\Setting::getValue('address', 'Near Ritz Hotel');
        $city = \App\Models\Setting::getValue('city', 'Accra');
        $phone = \App\Models\Setting::getValue('phone');
        $email = \App\Models\Setting::getValue('email');
        $logo = \App\Models\Setting::getValue('logo');
        $favicon = \App\Models\Setting::getValue('favicon');

        // Stats
        $memberCount = \App\Models\Member::where('is_active', true)->count();
        $ministryCount = \App\Models\Ministry::where('is_active', true)->count();
        $bibleStudyGroupCount = \App\Models\BibleStudyGroup::where('is_active', true)->count();
        $volunteerCount = \App\Models\Member::where('is_active', true)->count();

        // Ministries (show top 8 active groups)
        $ministries = \App\Models\Ministry::where('is_active', true)
            ->where('type', 'group')
            ->limit(8)
            ->get();

        // Service times from settings
        $bibleStudyDay = \App\Models\Setting::getValue('bible_study_day', 'Wednesday');
        $serviceTimes = [
            ['name' => 'Sunday School', 'time' => \App\Models\Setting::getValue('sunday_school_time', '8:00 AM'), 'day' => 'Sunday'],
            ['name' => 'Morning Worship', 'time' => \App\Models\Setting::getValue('morning_worship_time', '9:30 AM'), 'day' => 'Sunday'],
            ['name' => 'Afternoon Service', 'time' => \App\Models\Setting::getValue('afternoon_service_time', '5:00 PM'), 'day' => 'Sunday'],
            ['name' => 'Bible Study', 'time' => \App\Models\Setting::getValue('bible_study_time', '6:30 PM'), 'day' => $bibleStudyDay],
        ];
    ?>
    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($favicon): ?>
    <link rel="icon" type="image/png" href="<?php echo e(Storage::disk('public')->url($favicon)); ?>">
    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
    <style>
        * { font-family: 'Inter', sans-serif; }

        .hero-section {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 50%, #1e1b4b 100%);
            min-height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .carousel {
            position: relative;
            width: 100%;
            height: 500px;
            overflow: hidden;
        }

        .carousel-slide {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            transition: opacity 1s ease-in-out;
        }

        .carousel-slide.active {
            opacity: 1;
        }

        .carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .carousel-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(to bottom, rgba(15, 23, 42, 0.3), rgba(15, 23, 42, 0.7));
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .carousel-content {
            text-align: center;
            color: white;
            max-width: 800px;
            padding: 2rem;
        }

        .carousel-content h2 {
            font-size: 3rem;
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .carousel-content p {
            font-size: 1.25rem;
            opacity: 0.9;
            margin-bottom: 2rem;
        }

        .carousel-dots {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 0.5rem;
        }

        .carousel-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: rgba(255,255,255,0.4);
            cursor: pointer;
            transition: all 0.3s;
        }

        .carousel-dot.active {
            background: white;
            transform: scale(1.2);
        }

        .nav-buttons {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border: none;
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .nav-buttons:hover {
            background: rgba(255,255,255,0.2);
        }

        .nav-prev { left: 1rem; }
        .nav-next { right: 1rem; }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            color: white;
            padding: 1rem 2rem;
            border-radius: 0.5rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.4);
        }

        .features-section {
            background: white;
            padding: 5rem 2rem;
        }

        .feature-card {
            background: #f8fafc;
            border-radius: 1rem;
            padding: 2rem;
            text-align: center;
            transition: all 0.3s;
            border: 1px solid #e2e8f0;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            border-color: #3b82f6;
        }

        .feature-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 1.5rem;
            background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .footer {
            background: #0f172a;
            color: white;
            padding: 3rem 2rem;
            text-align: center;
        }

        .stats-section {
            background: linear-gradient(135deg, #1e3a5f 0%, #0f172a 100%);
            padding: 4rem 2rem;
            color: white;
        }

        .stat-card {
            text-align: center;
            padding: 2rem;
        }

        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            background: linear-gradient(135deg, #60a5fa 0%, #3b82f6 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-fade-in {
            animation: fadeIn 1s ease-out;
        }

        .service-times {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 1rem;
            padding: 2rem;
            margin-top: 2rem;
        }

        .service-time-item {
            display: flex;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }

        .service-time-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <div class="hero-section">
        <div class="relative z-10">
            <nav class="flex items-center justify-between px-6 py-3 max-w-7xl mx-auto">
                <div class="flex items-center gap-3">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($logo): ?>
                        <img src="<?php echo e(Storage::disk('public')->url($logo)); ?>" alt="<?php echo e($appName); ?>" class="h-10 w-10 rounded-full object-cover">
                    <?php else: ?>
                    <div class="w-12 h-12 bg-white/10 backdrop-blur rounded-lg flex items-center justify-center">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                    </div>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                    <div class="text-white">
                        <h1 class="text-xl font-bold"><?php echo e($appName); ?></h1>
                        <p class="text-sm text-blue-200"><?php echo e($tagline); ?></p>
                    </div>
                </div>
                <div class="flex items-center gap-4">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if(auth()->guard()->check()): ?>
                        <a href="<?php echo e(url('/admin')); ?>" class="btn-primary">Dashboard</a>
                    <?php else: ?>
                        <a href="<?php echo e(route('login')); ?>" class="text-white hover:text-blue-200 transition">Login</a>
                        <a href="<?php echo e(url('/admin')); ?>" class="btn-primary">Get Started</a>
                    <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
            </nav>

            <div class="carousel">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sliders->count() > 0): ?>
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-slide <?php echo e($index === 0 ? 'active' : ''); ?>">
                        <img src="<?php echo e(Storage::disk('public')->url($slide->image)); ?>" alt="<?php echo e($slide->title); ?>">
                        <div class="carousel-overlay">
                            <div class="carousel-content animate-fade-in">
                                <h2><?php echo e($slide->title); ?></h2>
                                <p><?php echo e($slide->description); ?></p>
                                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($slide->link): ?>
                                    <a href="<?php echo e($slide->link); ?>" class="btn-primary">Learn More</a>
                                <?php else: ?>
                                    <a href="<?php echo e(url('/admin')); ?>" class="btn-primary">Join Us This Sunday</a>
                                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php else: ?>
                    <div class="carousel-slide active">
                        <img src="https://images.unsplash.com/photo-1438232992991-995b7058bbb3?w=1920&h=1080&fit=crop" alt="Church">
                        <div class="carousel-overlay">
                            <div class="carousel-content animate-fade-in">
                                <h2>Welcome to <?php echo e($appName); ?></h2>
                                <p><?php echo e($tagline); ?></p>
                                <a href="<?php echo e(url('/admin')); ?>" class="btn-primary">Join Us This Sunday</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>

                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($sliders->count() > 1): ?>
                <button class="nav-buttons nav-prev" onclick="changeSlide(-1)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </button>
                <button class="nav-buttons nav-next" onclick="changeSlide(1)">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>

                <div class="carousel-dots">
                    <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $sliders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $slide): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="carousel-dot <?php echo e($index === 0 ? 'active' : ''); ?>" onclick="goToSlide(<?php echo e($index); ?>)"></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="stats-section">
        <div class="max-w-7xl mx-auto">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($memberCount); ?>+</div>
                    <div class="text-blue-200 mt-2">Members</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($ministryCount); ?></div>
                    <div class="text-blue-200 mt-2">Ministries</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($bibleStudyGroupCount); ?></div>
                    <div class="text-blue-200 mt-2">Bible Study Groups</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo e($volunteerCount); ?>+</div>
                    <div class="text-blue-200 mt-2">Volunteers</div>
                </div>
            </div>
        </div>
    </div>

    <div class="features-section">
        <div class="max-w-7xl mx-auto">
            <div class="text-center mb-12">
                <h2 class="text-4xl font-bold text-gray-900 mb-4">Our Ministries</h2>
                <p class="text-xl text-gray-600">Find your place to serve and grow</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__empty_1 = true; $__currentLoopData = $ministries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ministry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2"><?php echo e($ministry->name); ?></h3>
                    <p class="text-gray-600"><?php echo e(Str::limit($ministry->description, 80) ?: 'Join us in fellowship and service'); ?></p>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                <div class="feature-card">
                    <div class="feature-icon">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Fellowship</h3>
                    <p class="text-gray-600">Building community through fellowship and service</p>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="bg-gradient-to-r from-blue-900 to-indigo-900 py-16">
        <div class="max-w-4xl mx-auto px-8 text-center text-white">
            <h2 class="text-4xl font-bold mb-6">Service Times</h2>
            <div class="service-times max-w-md mx-auto">
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php $__currentLoopData = $serviceTimes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $service): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <div class="service-time-item">
                    <span class="font-semibold"><?php echo e($service['day']); ?> - <?php echo e($service['name']); ?></span>
                    <span class="text-blue-200"><?php echo e($service['time']); ?></span>
                </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
            </div>
        </div>
    </div>

    <div class="features-section">
        <div class="max-w-4xl mx-auto text-center">
            <h2 class="text-4xl font-bold text-gray-900 mb-4">Visit Us</h2>
            <p class="text-xl text-gray-600 mb-8">
                <?php echo e($description ?: "We'd love to welcome you to our church family. Join us for worship, fellowship, and encouragement."); ?>

            </p>
            <div class="bg-blue-50 rounded-2xl p-8 inline-block">
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-lg text-gray-700"><?php echo e($address); ?><?php echo e($city ? ', ' . $city : ''); ?></span>
                </div>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($phone): ?>
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <span class="text-lg text-gray-700"><?php echo e($phone); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if BLOCK]><![endif]--><?php endif; ?><?php if($email): ?>
                <div class="flex items-center gap-3 mb-4">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                    <span class="text-lg text-gray-700"><?php echo e($email); ?></span>
                </div>
                <?php endif; ?><?php if(\Livewire\Mechanisms\ExtendBlade\ExtendBlade::isRenderingLivewireComponent()): ?><!--[if ENDBLOCK]><![endif]--><?php endif; ?>
                <a href="<?php echo e(url('/admin')); ?>" class="btn-primary">Plan Your Visit</a>
            </div>
        </div>
    </div>

    <div class="footer">
        <p class="text-gray-400">&copy; <?php echo e(date('Y')); ?> <?php echo e($appName); ?>. All rights reserved.</p>
        <p class="text-gray-500 text-sm mt-2">Powered by Church Management System</p>
    </div>

    <script>
        let currentSlide = 0;
        const slides = document.querySelectorAll('.carousel-slide');
        const dots = document.querySelectorAll('.carousel-dot');

        function showSlide(index) {
            slides.forEach((slide, i) => {
                slide.classList.remove('active');
                if (dots[i]) dots[i].classList.remove('active');
            });
            slides[index].classList.add('active');
            if (dots[index]) dots[index].classList.add('active');
        }

        function changeSlide(direction) {
            currentSlide = (currentSlide + direction + slides.length) % slides.length;
            showSlide(currentSlide);
        }

        function goToSlide(index) {
            currentSlide = index;
            showSlide(currentSlide);
        }

        if (slides.length > 1) {
            setInterval(() => changeSlide(1), 5000);
        }
    </script>
</body>
</html>
<?php /**PATH C:\Users\user\Desktop\projects\church-app-v2\resources\views/welcome.blade.php ENDPATH**/ ?>