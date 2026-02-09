{{-- @extends('errors::minimal')

@section('title', __('Not Found'))
@section('code', '404')
@section('message', __('Not Found')) --}}


<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>404 - Page Not Found</title>
	<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
	<style>
		* {
			margin: 0;
			padding: 0;
			box-sizing: border-box;
		}

		body {
			font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			min-height: 100vh;
			display: flex;
			align-items: center;
			justify-content: center;
			overflow: hidden;
			position: relative;
		}

		/* Animated stars background */
		.stars {
			position: absolute;
			width: 100%;
			height: 100%;
			overflow: hidden;
		}

		.star {
			position: absolute;
			width: 3px;
			height: 3px;
			background: white;
			border-radius: 50%;
			animation: twinkle 3s infinite ease-in-out;
		}

		.star:nth-child(1) { top: 20%; left: 10%; animation-delay: 0s; }
		.star:nth-child(2) { top: 40%; left: 30%; animation-delay: 0.5s; }
		.star:nth-child(3) { top: 60%; left: 70%; animation-delay: 1s; }
		.star:nth-child(4) { top: 80%; left: 50%; animation-delay: 1.5s; }
		.star:nth-child(5) { top: 30%; left: 80%; animation-delay: 2s; }
		.star:nth-child(6) { top: 70%; left: 20%; animation-delay: 2.5s; }
		.star:nth-child(7) { top: 15%; left: 60%; animation-delay: 0.8s; }
		.star:nth-child(8) { top: 85%; left: 85%; animation-delay: 1.8s; }

		@keyframes twinkle {
			0%, 100% { opacity: 0.3; transform: scale(1); }
			50% { opacity: 1; transform: scale(1.5); }
		}

		/* Floating cloud-like shapes */
		.cloud {
			position: absolute;
			background: rgba(255, 255, 255, 0.08);
			border-radius: 100px;
			animation: float-cloud 25s infinite ease-in-out;
		}

		.cloud:nth-child(9) {
			width: 350px;
			height: 120px;
			top: 10%;
			left: -100px;
			animation-delay: 0s;
		}

		.cloud:nth-child(10) {
			width: 250px;
			height: 90px;
			bottom: 15%;
			right: -80px;
			animation-delay: 5s;
		}

		@keyframes float-cloud {
			0%, 100% { transform: translateX(0) translateY(0); }
			50% { transform: translateX(50px) translateY(-20px); }
		}

		.error-container {
			position: relative;
			z-index: 1;
			text-align: center;
			padding: 50px 40px;
			background: rgba(255, 255, 255, 0.95);
			backdrop-filter: blur(20px);
			border-radius: 30px;
			box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
			max-width: 650px;
			width: 90%;
			animation: bounceIn 0.8s ease-out;
		}

		@keyframes bounceIn {
			0% {
				opacity: 0;
				transform: scale(0.3);
			}
			50% {
				opacity: 1;
				transform: scale(1.05);
			}
			70% {
				transform: scale(0.9);
			}
			100% {
				transform: scale(1);
			}
		}

		.astronaut-icon {
			width: 140px;
			height: 140px;
			margin: 0 auto 25px;
			animation: float-astronaut 3s infinite ease-in-out;
		}

		@keyframes float-astronaut {
			0%, 100% { transform: translateY(0px) rotate(0deg); }
			50% { transform: translateY(-20px) rotate(5deg); }
		}

		.astronaut-icon svg {
			width: 100%;
			height: 100%;
		}

		.error-code {
			font-size: 120px;
			font-weight: 700;
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			-webkit-background-clip: text;
			-webkit-text-fill-color: transparent;
			background-clip: text;
			margin-bottom: 10px;
			line-height: 1;
			letter-spacing: -5px;
		}

		.error-title {
			font-size: 36px;
			font-weight: 600;
			color: #2d3748;
			margin-bottom: 15px;
		}

		.error-message {
			font-size: 17px;
			color: #718096;
			line-height: 1.7;
			margin-bottom: 35px;
			max-width: 500px;
			margin-left: auto;
			margin-right: auto;
		}

		.search-box {
			margin-bottom: 30px;
			display: flex;
			gap: 10px;
			max-width: 450px;
			margin-left: auto;
			margin-right: auto;
		}

		.search-input {
			flex: 1;
			padding: 14px 20px;
			border: 2px solid #e2e8f0;
			border-radius: 12px;
			font-size: 15px;
			font-family: inherit;
			transition: all 0.3s ease;
			outline: none;
		}

		.search-input:focus {
			border-color: #f093fb;
			box-shadow: 0 0 0 3px rgba(240, 147, 251, 0.1);
		}

		.search-btn {
			padding: 14px 24px;
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			color: white;
			border: none;
			border-radius: 12px;
			font-size: 15px;
			font-weight: 600;
			cursor: pointer;
			transition: all 0.3s ease;
			font-family: inherit;
		}

		.search-btn:hover {
			transform: translateY(-2px);
			box-shadow: 0 5px 15px rgba(245, 87, 108, 0.4);
		}

		.button-group {
			display: flex;
			gap: 12px;
			justify-content: center;
			flex-wrap: wrap;
			margin-bottom: 30px;
		}

		.btn {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			padding: 13px 28px;
			font-size: 15px;
			font-weight: 600;
			text-decoration: none;
			border-radius: 12px;
			transition: all 0.3s ease;
			cursor: pointer;
			border: none;
			font-family: inherit;
		}

		.btn-primary {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			color: white;
			box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
		}

		.btn-primary:hover {
			transform: translateY(-2px);
			box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
		}

		.btn-secondary {
			background: white;
			color: #f5576c;
			border: 2px solid #f5576c;
		}

		.btn-secondary:hover {
			background: #f5576c;
			color: white;
			transform: translateY(-2px);
		}

		.quick-links {
			margin-top: 25px;
		}

		.quick-links-title {
			font-size: 14px;
			color: #a0aec0;
			margin-bottom: 15px;
			text-transform: uppercase;
			letter-spacing: 1px;
		}

		.links-grid {
			display: flex;
			gap: 10px;
			justify-content: center;
			flex-wrap: wrap;
		}

		.quick-link {
			padding: 8px 16px;
			background: #f7fafc;
			color: #4a5568;
			text-decoration: none;
			border-radius: 8px;
			font-size: 14px;
			transition: all 0.3s ease;
		}

		.quick-link:hover {
			background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
			color: white;
			transform: translateY(-2px);
		}

		@media (max-width: 600px) {
			.error-container {
				padding: 40px 25px;
			}

			.error-code {
				font-size: 80px;
				letter-spacing: -3px;
			}

			.error-title {
				font-size: 28px;
			}

			.astronaut-icon {
				width: 110px;
				height: 110px;
			}

			.search-box {
				flex-direction: column;
			}

			.button-group {
				flex-direction: column;
				width: 100%;
			}

			.btn {
				width: 100%;
				justify-content: center;
			}

			.links-grid {
				flex-direction: column;
			}

			.quick-link {
				text-align: center;
			}
		}
	</style>
</head>

<body>
	<div class="stars">
		<div class="star"></div>
		<div class="star"></div>
		<div class="star"></div>
		<div class="star"></div>
		<div class="star"></div>
		<div class="star"></div>
		<div class="star"></div>
		<div class="star"></div>
		<div class="cloud"></div>
		<div class="cloud"></div>
	</div>

	<div class="error-container">
		<div class="astronaut-icon">
			<svg viewBox="0 0 200 200" xmlns="http://www.w3.org/2000/svg">
				<!-- Astronaut helmet -->
				<circle cx="100" cy="80" r="45" fill="#f093fb" opacity="0.2"/>
				<circle cx="100" cy="80" r="40" fill="white" stroke="#f5576c" stroke-width="3"/>
				
				<!-- Face -->
				<circle cx="90" cy="75" r="4" fill="#2d3748"/>
				<circle cx="110" cy="75" r="4" fill="#2d3748"/>
				<path d="M 90 85 Q 100 90 110 85" stroke="#f5576c" stroke-width="2" fill="none"/>
				
				<!-- Body -->
				<rect x="75" y="120" width="50" height="60" rx="10" fill="#f093fb"/>
				<circle cx="100" cy="150" r="8" fill="white"/>
				
				<!-- Arms -->
				<rect x="55" y="125" width="15" height="40" rx="7" fill="#f5576c"/>
				<rect x="130" y="125" width="15" height="40" rx="7" fill="#f5576c"/>
				
				<!-- Antenna -->
				<line x1="100" y1="35" x2="100" y2="25" stroke="#f5576c" stroke-width="2"/>
				<circle cx="100" cy="22" r="4" fill="#f093fb"/>
			</svg>
		</div>

		<div class="error-code">404</div>
		<h1 class="error-title">Page Not Found</h1>
		<p class="error-message">
			Oops! The page you're looking for seems to have wandered off into space. Let's help you find your way back home.
		</p>

		<form class="search-box" action="{{url('/search')}}" method="GET">
			<input type="text" name="q" class="search-input" placeholder="Search for what you need..." />
			<button type="submit" class="search-btn">Search</button>
		</form>

		<div class="button-group">
			<a href="{{url('/home')}}" class="btn btn-primary">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
					<polyline points="9 22 9 12 15 12 15 22"></polyline>
				</svg>
				Back to Home
			</a>
			<a href="javascript:history.back()" class="btn btn-secondary">
				<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
					<line x1="19" y1="12" x2="5" y2="12"></line>
					<polyline points="12 19 5 12 12 5"></polyline>
				</svg>
				Go Back
			</a>
		</div>

		<div class="quick-links">
			<p class="quick-links-title">Quick Links</p>
			<div class="links-grid">
				<a href="{{url('/home')}}" class="quick-link">Dashboard</a>
				<a href="{{url('/products')}}" class="quick-link">Products</a>
				<a href="{{url('/orders')}}" class="quick-link">Orders</a>
				<a href="{{url('/contact')}}" class="quick-link">Contact Us</a>
			</div>
		</div>
	</div>
</body>

</html>

