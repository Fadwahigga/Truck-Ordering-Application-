import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../services/auth_service.dart';

class LoginScreen extends StatefulWidget {
  const LoginScreen({super.key});

  @override
  _LoginScreenState createState() => _LoginScreenState();
}

class _LoginScreenState extends State<LoginScreen> {
  final TextEditingController _emailController = TextEditingController();
  final TextEditingController _passwordController = TextEditingController();
  bool _isLoading = false;

  void _showDialog(String title, String message, Icon icon,
      {bool isSuccess = false}) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: Row(
            children: [
              icon,
              const SizedBox(width: 10),
              Text(title),
            ],
          ),
          content: Text(message),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
                if (isSuccess) {
                  Navigator.pushReplacementNamed(context, '/dashboard');
                }
              },
              child: const Text('OK'),
            ),
          ],
        );
      },
    );
  }

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Login'),
        centerTitle: true,
      ),
      body: Padding(
        padding: const EdgeInsets.all(16.0),
        child: Column(
          mainAxisAlignment: MainAxisAlignment.center,
          children: [
            TextField(
              controller: _emailController,
              decoration: InputDecoration(
                label: RichText(
                  text: const TextSpan(
                    text: 'Email',
                    style: TextStyle(color: Colors.black),
                    children: [
                      TextSpan(
                        text: ' *',
                        style: TextStyle(color: Colors.red),
                      ),
                    ],
                  ),
                ),
              ),
            ),
            TextField(
              controller: _passwordController,
              decoration: InputDecoration(
                label: RichText(
                  text: const TextSpan(
                    text: 'Password',
                    style: TextStyle(color: Colors.black),
                    children: [
                      TextSpan(
                        text: ' *',
                        style: TextStyle(color: Colors.red),
                      ),
                    ],
                  ),
                ),
              ),
              obscureText: true,
            ),
            const SizedBox(height: 20),
            _isLoading
                ? const CircularProgressIndicator()
                : ElevatedButton(
                    onPressed: () async {
                      final email = _emailController.text.trim();
                      final password = _passwordController.text.trim();
                      final authService =
                          Provider.of<AuthService>(context, listen: false);

                      if (email.isEmpty || password.isEmpty) {
                        _showDialog(
                          'Error',
                          'Please fill in all fields.',
                          const Icon(Icons.error, color: Colors.red),
                        );
                        return;
                      }

                      setState(() {
                        _isLoading = true;
                      });

                      final success = await authService.login(email, password);

                      setState(() {
                        _isLoading = false;
                      });

                      if (success) {
                        _showDialog(
                          'Success',
                          'Login successful!',
                          const Icon(Icons.check_circle, color: Colors.green),
                          isSuccess: true,
                        );
                      } else {
                        _showDialog(
                          'Error',
                          'Login failed. Please check your credentials.',
                          const Icon(Icons.error, color: Colors.red),
                        );
                      }
                    },
                    child: const Text('Login'),
                  ),
            const SizedBox(height: 20),
            TextButton(
              onPressed: () {
                Navigator.of(context).pushNamedAndRemoveUntil(
                  '/register',
                  (route) => false,
                );
              },
              child: const Text('Don\'t have an account? Register'),
            ),
          ],
        ),
      ),
    );
  }
}
