<div class="container">
    <a href="profile.php">Мой профиль</a>
    <h3>Catalog</h3>
    <div class="card-deck">
        <?php foreach ($products as $product) : ?>
            <a href="#" class="card text-center">
                <div class="card-header">
                    Hit!
                </div>
                <img class="card-img-top"
                     src="<?php echo htmlspecialchars($product['image_url']); ?>"
                     alt="Card image">
                <div class="card-body">
                    <!-- Название товара в заголовке (h5) -->
                    <h5 class="card-title">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h5>
                    <!-- Описание товара в параграфе -->
                    <p class="card-text text-muted">
                        <?php echo htmlspecialchars($product['description']); ?>
                    </p>
                </div>
                <div class="card-footer">
                    <?php echo htmlspecialchars($product['price']); ?>
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<style>
    body {
        font-family: sans-serif; /* было font-style, исправлено */
    }

    a {
        text-decoration: none;
    }

    a:hover {
        text-decoration: none;
    }

    h3 {
        line-height: 3em;
    }

    .card {
        max-width: 16rem;
        display: block; /* чтобы ссылка вела себя как блочный элемент */
    }

    .card:hover {
        box-shadow: 1px 2px 10px lightgray;
        transition: 0.2s;
    }

    .card-header {
        font-size: 13px;
        color: gray;
        background-color: white;
    }

    .text-muted {
        font-size: 11px;
    }

    .card-footer {
        font-weight: bold;
        font-size: 18px;
        background-color: white;
    }

    .card-body {
        flex: 1;
    }
</style>