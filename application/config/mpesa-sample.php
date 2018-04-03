<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * MPESA API (**DO NOT EDIT**)
 */
$config['live_url'] = "https://api.safaricom.co.ke/mpesa/";
$config['sandbox_url'] = "https://sandbox.safaricom.co.ke/mpesa/";

/**
 * MPESA Oauth Token generator URL (**DO NOT EDIT**)
 */
$config['live_token_url'] = "https://api.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";
$config['sandbox_token_url'] = "https://sandbox.safaricom.co.ke/oauth/v1/generate?grant_type=client_credentials";

/**
 * MPESA API credentials(**EDIT VALUES**)
 */
$config['consumer_key'] = "YOUR_CONSUMER_KEY";
$config['consumer_secret'] = "YOUR_CONSUMER_SECRET";

/**
 * APP settings
 */
$config['application_status'] = "sandbox"; //values: live, sandbox
