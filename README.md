# Restaurant Inventory API

A simple Laravel API for managing restaurant inventory and orders.

## Features
- Products with ingredient requirements
- Atomic order processing using DB transactions
- Automatic stock deduction
- Order fails if any ingredient is out of stock
- Low stock email alert (below 50%) using Events & Listeners
- Feature tests written with Pest

## Setup
```bash
composer install
php artisan migrate --seed
php artisan serve
````

## API

**POST** `/api/orders`

```json
{
  "items": [
    { "product_id": 1, "quantity": 2 }
  ]
}
```

## Testing

```bash
php artisan test
```

---

Built with Laravel 🚀