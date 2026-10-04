<?php
defined('PREVENT_DIRECT_ACCESS') OR exit('No direct script access allowed');

class AuthApiController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->call->library('api');
    }

    public function login()
    {
        $this->api->require_method('POST');
        $this->api->rate_limit();

        $body = $this->api->body();
        $username = $body['username'] ?? '';
        $password = $body['password'] ?? '';

        if (empty($username) || empty($password)) {
            $this->api->respond_error('Username and password are required.', 422);
        }

        $stmt = $this->db->raw(
            "SELECT id, username, password, role FROM users WHERE username = ? AND is_active = 1 LIMIT 1",
            [$username]
        );
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $this->api->respond_error('Invalid username or password.', 401);
        }

        $tokens = $this->api->issue_tokens([
            'id'   => $user['id'],
            'role' => $user['role'],
        ]);

        $this->api->respond([
            'message' => 'Login successful',
            'user' => [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'],
            ],
            'tokens' => $tokens
        ]);
    }

    public function refresh()
    {
        $this->api->require_method('POST');

        $body = $this->api->body();
        $refresh_token = $body['refresh_token'] ?? '';

        if (empty($refresh_token)) {
            $this->api->respond_error('Refresh token is required.', 422);
        }

        $this->api->refresh_access_token($refresh_token);
    }

    public function logout()
    {
        $this->api->require_method('POST');

        $body = $this->api->body();
        $refresh_token = $body['refresh_token'] ?? '';

        if (!empty($refresh_token)) {
            $this->api->revoke_refresh_token($refresh_token);
        }

        $this->api->respond(['message' => 'Logged out successfully']);
    }
}