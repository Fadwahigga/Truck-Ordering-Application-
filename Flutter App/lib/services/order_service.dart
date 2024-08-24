import 'dart:convert';

import 'package:flutter/material.dart';
import 'package:http/http.dart' as http;

import 'auth_service.dart';

class Order {
  final int id;
  final String status;

  Order({required this.id, required this.status});

  factory Order.fromJson(Map<String, dynamic> json) {
    return Order(
      id: json['id'],
      status: json['status'],
    );
  }
}

class OrderService extends ChangeNotifier {
  final String baseUrl = 'http://192.168.10.26:8000/api';
  final AuthService _authService;

  OrderService(this._authService);

  Future<List<Order>> getOrders() async {
    final token = await _authService.getToken();
    final response = await http.get(
      Uri.parse('$baseUrl/orders'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
    );

    if (response.statusCode == 200) {
      final data = jsonDecode(response.body) as List;
      return data.map((order) => Order.fromJson(order)).toList();
    } else {
      throw Exception('Failed to load orders');
    }
  }

  Future<void> createOrder({
    required String pickupLocation,
    required String deliveryLocation,
    required String size,
    required String weight,
    required DateTime pickupTime,
    required DateTime deliveryTime,
  }) async {
    final token = await _authService.getToken();
    final response = await http.post(
      Uri.parse('$baseUrl/orders'),
      headers: {
        'Content-Type': 'application/json',
        'Authorization': 'Bearer $token',
      },
      body: jsonEncode({
        'pickup_location': pickupLocation,
        'delivery_location': deliveryLocation,
        'size': size,
        'weight': weight,
        'pickup_time': pickupTime.toIso8601String(),
        'delivery_time': deliveryTime.toIso8601String(),
      }),
    );
    if (response.statusCode == 200 || response.statusCode == 201) {
      notifyListeners();
    } else {
      throw Exception('Failed to create order');
    }
  }
}
