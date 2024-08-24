// ignore_for_file: library_private_types_in_public_api

import 'package:flutter/material.dart';
import 'package:provider/provider.dart';

import '../services/order_service.dart';

class TruckRequestScreen extends StatefulWidget {
  const TruckRequestScreen({super.key});

  @override
  _TruckRequestScreenState createState() => _TruckRequestScreenState();
}

class _TruckRequestScreenState extends State<TruckRequestScreen> {
  final TextEditingController _pickupLocationController =
      TextEditingController();
  final TextEditingController _deliveryLocationController =
      TextEditingController();
  final TextEditingController _sizeController = TextEditingController();
  final TextEditingController _weightController = TextEditingController();
  DateTime? _pickupTime;
  DateTime? _deliveryTime;

  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: const Text('Request a Truck'),
        centerTitle: true,
      ),
      body: SingleChildScrollView(
        child: Padding(
          padding: const EdgeInsets.all(20.0),
          child: Column(
            children: [
              TextField(
                controller: _pickupLocationController,
                decoration: const InputDecoration(
                  label: Row(
                    children: [
                      Text('Pickup Location'),
                      Text(
                        ' *',
                        style: TextStyle(color: Colors.red),
                      ),
                    ],
                  ),
                ),
              ),
              TextField(
                controller: _deliveryLocationController,
                decoration: const InputDecoration(
                  label: Row(
                    children: [
                      Text('Delivery Location'),
                      Text(
                        ' *',
                        style: TextStyle(color: Colors.red),
                      ),
                    ],
                  ),
                ),
              ),
              TextField(
                controller: _sizeController,
                decoration: const InputDecoration(
                  label: Row(
                    children: [
                      Text('Size'),
                      Text(
                        ' *',
                        style: TextStyle(color: Colors.red),
                      ),
                    ],
                  ),
                ),
              ),
              TextField(
                controller: _weightController,
                decoration: const InputDecoration(
                  label: Row(
                    children: [
                      Text('Weight'),
                      Text(
                        ' *',
                        style: TextStyle(color: Colors.red),
                      ),
                    ],
                  ),
                ),
              ),
              Padding(
                padding: const EdgeInsets.all(8.0),
                child: ElevatedButton(
                  onPressed: () async {
                    final DateTime? picked = await showDatePicker(
                      context: context,
                      initialDate: DateTime.now(),
                      firstDate: DateTime(2000),
                      lastDate: DateTime(2101),
                    );
                    if (picked != null) setState(() => _pickupTime = picked);
                  },
                  style: ElevatedButton.styleFrom(
                    foregroundColor: Colors.white,
                    backgroundColor: Colors.blue,
                    padding: const EdgeInsets.symmetric(
                        vertical: 15.0, horizontal: 20.0),
                    textStyle: const TextStyle(fontSize: 16),
                    shape: RoundedRectangleBorder(
                      borderRadius: BorderRadius.circular(10),
                    ),
                  ),
                  child: const Text('Select Pickup Time'),
                ),
              ),
              const SizedBox(height: 10),
              ElevatedButton(
                onPressed: () async {
                  final DateTime? picked = await showDatePicker(
                    context: context,
                    initialDate: DateTime.now(),
                    firstDate: DateTime(2000),
                    lastDate: DateTime(2101),
                  );
                  if (picked != null) setState(() => _deliveryTime = picked);
                },
                style: ElevatedButton.styleFrom(
                  foregroundColor: Colors.white,
                  backgroundColor: Colors.blue,
                  padding: const EdgeInsets.symmetric(
                      vertical: 15.0, horizontal: 20.0),
                  textStyle: const TextStyle(fontSize: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
                child: const Text('Select Delivery Time'),
              ),
              const SizedBox(height: 20),
              ElevatedButton(
                onPressed: () {
                  if (_pickupTime == null || _deliveryTime == null) {
                    _showErrorDialog(
                        context, 'Please set both Pickup and Delivery times.');
                    return;
                  }
                  final orderService =
                      Provider.of<OrderService>(context, listen: false);
                  orderService
                      .createOrder(
                    pickupLocation: _pickupLocationController.text,
                    deliveryLocation: _deliveryLocationController.text,
                    size: _sizeController.text,
                    weight: _weightController.text,
                    pickupTime: _pickupTime!,
                    deliveryTime: _deliveryTime!,
                  )
                      .then((_) {
                    _showSuccessDialog(context);
                  }).catchError((error) {
                    _showErrorDialog(context, error.toString());
                  });
                },
                style: ElevatedButton.styleFrom(
                  foregroundColor: Colors.white,
                  backgroundColor: Colors.green,
                  padding: const EdgeInsets.symmetric(
                      vertical: 15.0, horizontal: 20.0),
                  textStyle: const TextStyle(fontSize: 16),
                  shape: RoundedRectangleBorder(
                    borderRadius: BorderRadius.circular(10),
                  ),
                ),
                child: const Text('Submit Request'),
              )
            ],
          ),
        ),
      ),
    );
  }

  void _showSuccessDialog(BuildContext context) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.check_circle, color: Colors.green),
              SizedBox(width: 10),
              Text('Success'),
            ],
          ),
          content: const Text('Order created successfully!'),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pushNamedAndRemoveUntil(
                  '/dashboard',
                  (route) => false,
                );
              },
              child: const Text('OK'),
            ),
          ],
        );
      },
    );
  }

  void _showErrorDialog(BuildContext context, String error) {
    showDialog(
      context: context,
      builder: (context) {
        return AlertDialog(
          title: const Row(
            children: [
              Icon(Icons.error, color: Colors.red),
              SizedBox(width: 10),
              Text('Error'),
            ],
          ),
          content: Text(error),
          actions: [
            TextButton(
              onPressed: () {
                Navigator.of(context).pop();
              },
              child: const Text('OK'),
            ),
          ],
        );
      },
    );
  }
}
