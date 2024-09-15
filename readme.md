
# Artykuły

---

## Opis Pakietu

Pakiet **"Artykuły"** integruje aplikację Laravel z zewnętrznym serwisem WordPress przez API, umożliwiając pobieranie artykułów, wyświetlanie szczegółów artykułu oraz powiązanych artykułów na podstawie kategorii. Pakiet obsługuje strony zarówno jednojęzyczne, jak i wielojęzyczne, automatycznie dostosowując kategorie artykułów do aktualnego języka lub ustawień strony.

---

## Funkcjonalności

1. **Pobieranie listy artykułów** – Pobiera artykuły z określonej kategorii WordPress na podstawie konfiguracji w pliku `config/articles.php`.
2. **Wyświetlanie szczegółów artykułu** – Wyświetla pojedynczy artykuł, sprawdzając, czy należy do odpowiedniej kategorii.
3. **Pobieranie powiązanych artykułów** – Wyświetla powiązane artykuły na podstawie tej samej kategorii.
4. **Obsługa obrazków** – Obsługuje zewnętrzne obrazki z serwera WordPress przez przekierowanie URL.
5. **Elastyczność językowa** – Obsługuje strony jednojęzyczne oraz wielojęzyczne z automatycznym przypisaniem kategorii na podstawie bieżącego języka.

---

## Instalacja Pakietu

1. **Zainstaluj pakiet przez Composer**:

   ```bash
   composer require pawel/laravel-articles
   ```

2. **Publikacja pliku konfiguracyjnego**:

   Po zainstalowaniu pakietu, opublikuj plik konfiguracyjny:

   ```bash
   php artisan vendor:publish --provider="Pawel\Articles\ArticlesServiceProvider" --tag="config"
   ```
   lub tylko
   ```bash
   php artisan vendor:publish 
   ```

   Plik konfiguracyjny zostanie opublikowany w katalogu `config/articles.php`.

3. **Konfiguracja w pliku `config/articles.php`**:

   W pliku konfiguracyjnym możesz skonfigurować dane dostępowe do API WordPress oraz kategorie dla różnych języków:

   ```php
   return [
       'wordpress' => [
           'api_url' => 'https://adres_wp.pl',
           'api_user' => 'nazwa_uzytkownika',
           'api_pass' => 'haslo_api',
           'jwt_user' => 'jwt_uzytkownik',
           'jwt_pass' => 'jwt_haslo',
           'categories' => [
               'default' => 8, // ID domyślnej kategorii dla stron jednojęzycznych
               'pl' => 8,      // ID kategorii dla polskich artykułów
               'en' => 9,      // ID kategorii dla angielskich artykułów
               'es' => 10,     // ID kategorii dla hiszpańskich artykułów
           ],
           'multilingual' => false, // Czy strona jest wielojęzyczna
       ],
   ];
   ```

   - **`api_url`**: Adres API WordPressa.
   - **`api_user` oraz `api_pass`**: Dane do autoryzacji przez Basic Auth.
   - **`categories`**: ID kategorii dla każdego języka lub dla stron jednojęzycznych.
   - **`multilingual`**: Flaga wskazująca, czy strona obsługuje wiele języków.

---

## Routing

W pliku `routes/web.php` możesz zdefiniować trasy do wyświetlania artykułów i obrazków:

```php
use Illuminate\Support\Facades\Route;
use Pawel\Articles\Http\Controllers\ArticleController;

Route::middleware(['web'])->group(function () {
    if (config('articles.wordpress.multilingual')) {
        Route::group([
            'prefix' => LaravelLocalization::setLocale(),
            'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
        ], function () {
            Route::get('/artykuly', [ArticleController::class, 'index'])->name('articles.index');
            Route::get('/artykuly/{slug}', [ArticleController::class, 'show'])->name('articles.show');
        });
    } else {
        Route::get('/artykuly', [ArticleController::class, 'index'])->name('articles.index');
        Route::get('/artykuly/{slug}', [ArticleController::class, 'show'])->name('articles.show');
    }

    // Trasa do obsługi zewnętrznych obrazków z WordPressa
    Route::get('/wp-images/{path}', function ($path) {
        $url = "https://api.museann.pl/wp-content/uploads/" . $path;
        return response()->stream(function () use ($url) {
            echo file_get_contents($url);
        }, 200, [
            'Content-Type' => 'image/jpeg',
            'Cache-Control' => 'public, max-age=2592000',
            'Expires' => gmdate('D, d M Y H:i:s \G\M\T', time() + 2592000),
        ]);
    })->where('path', '.*');
});
```

---

## Użycie

### 1. **Pobieranie artykułów**

Metoda `index()` w kontrolerze `ArticleController` pobiera listę artykułów z odpowiedniej kategorii w zależności od konfiguracji języka.

### 2. **Wyświetlanie pojedynczego artykułu**

Metoda `show($slug)` w kontrolerze `ArticleController` wyświetla artykuł na podstawie jego slug i sprawdza, czy artykuł należy do odpowiedniej kategorii.

### 3. **Powiązane artykuły**

Podczas wyświetlania szczegółów artykułu, pakiet automatycznie pobiera artykuły powiązane, które należą do tej samej kategorii, co bieżący artykuł.

### 4. **Obsługa obrazków**

Pakiet obsługuje obrazki z zewnętrznego serwera WordPress, przekierowując URL do lokalnego zasobu `/wp-images/{path}`, dzięki czemu obrazki są pobierane bezpośrednio z API WordPressa.

---

## Wsparcie dla Stron Wielojęzycznych

- **Wielojęzyczność**: Pakiet obsługuje różne kategorie artykułów w zależności od języka, dynamicznie przypisując kategorie na podstawie bieżącego języka.
- **Konfiguracja**: Ustaw w pliku `config/articles.php` kategorię dla każdego języka oraz ustaw `multilingual` na `true`, jeśli strona obsługuje wiele języków.

---

## Aktualizacja Pakietu

Aby zaktualizować pakiet, użyj poniższego polecenia:

```bash
composer update pawel/laravel-articles
```

Jeśli plik konfiguracyjny został zaktualizowany w nowej wersji, możesz go ponownie opublikować:

```bash
php artisan vendor:publish --provider="Pawel\Articles\ArticlesServiceProvider" --tag="config" --force
```

---

## Debugowanie

W przypadku problemów z połączeniem lub API możesz włączyć logowanie, dodając poniższy kod w odpowiednich miejscach:

```php
Log::info('Pobieranie artykułów', ['url' => $this->apiUrl]);
```

---

## Licencja

Pakiet jest dostępny na licencji MIT.
