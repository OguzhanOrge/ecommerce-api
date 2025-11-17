
---

````md
# Ecommerce API – Laravel

Laravel ile geliştirilmiş basit bir E-Ticaret REST API projesidir.  
Bu doküman proje kurulumu, veritabanı ayarları, endpoint listesi ve örnek istek/cevapları içerir.

---

## 🚀 Kurulum

### Gereksinimler
- PHP >= 8.x  
- Composer  
- Laravel >= 10.x  
- PostgreSQL  
- (Opsiyonel) Docker

### Kurulum Adımları
```bash
git clone <repo-url>
cd <proje-klasörü>
composer install
cp .env.example .env
php artisan key:generate
````

---

## 🗄️ Veritabanı Kurulumu

### .env Dosyası

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ecommerce
DB_USERNAME=root
DB_PASSWORD=
```

### Migrasyon + Seeder

```bash
php artisan migrate --seed
```

### Docker Kullanımı (Varsa)
(docker-compose dosyasını ve .env dosyasını configure etmelisiniz)

```bash
docker-compose up -d
```

---

# 🔗 API Endpoint Listesi

Aşağıda Postman koleksiyonundaki *tüm endpointlerin minimal listesi* yer almaktadır.

---

## 🔐 Authentication

| Method | Endpoint        | Açıklama                      |
| ------ | --------------- | ----------------------------- |
| POST   | `/api/register` | Yeni kullanıcı oluştur        |
| POST   | `/api/login`    | Kullanıcı girişi (JWT üretir) |
| POST   | `/api/logout`   | Çıkış yap                     |
| POST   | `/api/refresh`  | Token yenile                  |

---

## 🛒 Cart (Sepet)

| Method | Endpoint                        | Açıklama                              |
| ------ | ------------------------------- | ------------------------------------- |
| GET    | `/api/cart`                     | Aktif kullanıcının sepetini görüntüle |
| POST   | `/api/cart/add`                 | Sepete ürün ekle                      |
| PUT    | `/api/cart/update`              | Sepetteki ürün miktarını güncelle     |
| DELETE | `/api/cart/remove/{product_id}` | Sepetten ürün sil                     |
| DELETE | `/api/cart/clear`               | Sepeti tamamen boşalt                 |

---

## 📂 Categories (Kategoriler)

| Method | Endpoint               | Açıklama                 |
| ------ | ---------------------- | ------------------------ |
| GET    | `/api/categories`      | Tüm kategorileri listele |
| GET    | `/api/categories/{id}` | Kategori detay           |
| POST   | `/api/categories`      | Yeni kategori ekle       |
| PUT    | `/api/categories/{id}` | Kategori güncelle        |
| DELETE | `/api/categories/{id}` | Kategori sil             |

---

## 📂 Product(Ürünler)

| Method | Endpoint               | Açıklama                 |
| ------ | ---------------------- | ------------------------ |
| GET    | `/api/products`      | Tüm Ürün listele |
| GET    | `/api/products/{id}` | Ürün detay           |
| POST   | `/api/products`      | Yeni Ürün ekle       |
| PUT    | `/api/products/{id}` | Ürün güncelle        |
| DELETE | `/api/products/{id}` | Ürün sil             |

---

## 📂 User(Kullanıcılar)

| Method | Endpoint               | Açıklama                 |
| ------ | ---------------------- | ------------------------ |
| GET    | `/api/users`      | Tüm Kullanıcılar listele |
| GET    | `/api/users/{id}` | Kullanıcılar detay           |
| POST   | `/api/users`      | Yeni Kullanıcılar ekle       |
| PUT    | `/api/users/{id}` | Kullanıcılar güncelle        |
| DELETE | `/api/users/{id}` | Kullanıcılar sil             |

---

## 🧾 Orders (Siparişler)

| Method | Endpoint                | Açıklama                  |
| ------ | ----------------------- | ------------------------- |
| GET    | `/api/orders/`          | Giriş yapan kullanıcının sipariş detayını getir    |
| PUT    | `/api/orders/         ` | Sipariş oluştur           |
| PUT    | `/api/orders/{orderId}` | Sipariş detayını getir    |
| PUT    | `/api/orders/{orderId}` | Sipariş durumunu güncelle |

---

# 📦 Örnek Request / Response

### Login – Request

```json
{
  "email": "test@example.com",
  "password": "password"
}
```

### Login – Response

```json
	
{
  "success": true,
  "message": "İşlem başarılı.",
  "data": {
    "headers": {},
    "original": {
      "access_token": "xxxx",
      "token_type": "bearer",
      "expires_in": 3600
    },
    "exception": null
  },
  "errors": []
}
```

---

### Sepete Ürün Ekle – Request

```json
{
  "product_id": 6,
  "quantity": 2
}
```

### Sepete Ürün Ekle – Response

```json
{
  "success": true,
  "message": "Ürün sepete eklendi.",
  "data": {
    "cart_id": 1,
    "product_id": 6,
    "quantity": 2,
    "updated_at": "2025-11-17T13:35:58.000000Z",
    "created_at": "2025-11-17T13:35:58.000000Z",
    "id": 4
  },
  "errors": []
}
```

---

## 👤 Test Kullanıcıları(dump dosyasını repo içinde bulabilirsiniz)

**Admin**

```
email: admin@test.com
password: admin123
```

**User**

```
email: user@test.com
password: user123
```

---

## ▶️ Projeyi Çalıştırma

```bash
php artisan serve
```

---

## 📝 Notlar

* Tüm endpointler JSON formatında cevap döner.
* JWT token gerektiren endpointlerde header kullanılmalıdır:

  ```
  Authorization: Bearer <token>
  ```

---

