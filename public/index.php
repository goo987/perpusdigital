<?php
require_once __DIR__.'/../templates/header.php';

$auth    = new Auth($db->pdo());
$isLogin = $auth->check();
$user    = $isLogin ? $auth->user() : null;

$bookModel   = new BookModel($db->pdo());
$reviewModel = new ReviewModel($db->pdo());

$books = $bookModel->all();
?>

<style>

    body {
        background-color: #0b0b0c;
        color: #e5e5e5;
    }

    .page-title {
        font-family: 'Poppins', sans-serif;
        letter-spacing: 2px;
        text-shadow: 0 0 15px rgba(168, 85, 247, 0.5);
    }

    /* === CARD === */
    .shinigami-card {
        position: relative;
        overflow: hidden;
        transition: transform .35s ease, box-shadow .35s ease, border-color .35s ease;
        z-index: 1;
        border: 1px solid rgba(255, 255, 255, 0.07);
        background: linear-gradient(160deg, #0c0c0d, #111113 60%, #0c0c0d);
    }

    .shinigami-card:hover {
        transform: translateY(-6px) scale(1.03);
        box-shadow: 0 0 30px rgba(168, 85, 247, 0.35);
        z-index: 20;
        border-color: rgba(168, 85, 247, 0.4);
    }

    .shinigami-card::before {
        content: "";
        position: absolute;
        inset: 0;
        opacity: 0;
        background: radial-gradient(circle at 50% 0%, rgba(168, 85, 247, 0.18), transparent 75%);
        transition: opacity .45s ease;
        pointer-events: none;
        z-index: 5;
    }

    .shinigami-card:hover::before {
        opacity: 1;
    }

    /* STOCK BADGE */
    .stock-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        z-index: 50;
        pointer-events: none;
        font-size: 11px;
        padding: 4px 10px;
        border-radius: 9999px;
        font-weight: 600;
    }

    .cover-img {
        transition: transform .5s ease, filter .5s ease;
    }
    .cover-img:hover {
        transform: scale(1.08);
        filter: brightness(1.15);
    }
</style>

<div class="max-w-7xl mx-auto py-12 px-4">

    <h1 class="text-4xl font-extrabold mb-12 text-center text-purple-300 page-title">
        KOLEKSI BUKU
    </h1>

    <?php if (empty($books)): ?>
        <p class="text-center text-gray-400">Belum ada buku tersedia.</p>

    <?php else: ?>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-10">

        <?php foreach($books as $b): ?>

        <?php
            $ratingData  = $reviewModel->getBookRating($b['id']);
            $avgRating   = $ratingData['avg_rating'] ?? 0;
            $reviewCount = $ratingData['count_review'] ?? 0;
            $latestReview = $reviewModel->getLatestReview($b['id']);
        ?>

        <div class="shinigami-card rounded-xl p-5 relative flex flex-col h-full shadow-lg">

            <!-- STOCK BADGE -->
            <?php if (intval($b['stok']) <= 0): ?>
                <span class="stock-badge bg-red-600 text-white">STOK HABIS</span>
            <?php else: ?>
                <span class="stock-badge bg-green-600 text-white">STOK: <?= intval($b['stok']) ?></span>
            <?php endif; ?>

            <!-- COVER -->
            <div class="w-full h-72 bg-black/40 rounded-lg overflow-hidden flex items-center justify-center shadow-inner">
                <?php if (!empty($b['cover'])): ?>
                    <img src="uploads/cover/<?= htmlspecialchars($b['cover']) ?>"
                         class="cover-img w-full h-full object-cover brightness-95">
                <?php else: ?>
                    <span class="text-gray-500 text-sm">Tidak ada cover</span>
                <?php endif; ?>
            </div>

            <!-- JUDUL -->
            <h2 class="text-xl font-bold mt-4 text-white line-clamp-2 tracking-wide">
                <?= htmlspecialchars($b['judul']) ?>
            </h2>

            <!-- PENULIS -->
            <p class="text-gray-300 text-sm mt-1">
                Oleh: <span class="text-purple-300 font-medium"><?= htmlspecialchars($b['penulis']) ?></span>
            </p>

            <!-- RATING -->
            <div class="mt-3 flex items-center gap-1 text-yellow-400">
                <?php
                for ($i = 1; $i <= 5; $i++):
                    if ($avgRating >= $i) {
                        echo '<span class="text-yellow-400 text-lg">★</span>';
                    } elseif ($avgRating >= $i - 0.5) {
                        echo '<span class="text-yellow-300 text-lg">☆</span>';
                    } else {
                        echo '<span class="text-gray-700 text-lg">★</span>';
                    }
                endfor;
                ?>

                <span class="text-sm text-gray-400 ml-1">
                    (<?= number_format($avgRating, 1) ?>/5)
                </span>

                <?php if($reviewCount > 0): ?>
                    <span class="text-xs text-gray-500">• <?= $reviewCount ?> ulasan</span>
                <?php endif; ?>
            </div>

            <!-- KOMENTAR TERBARU (HANYA USER LOGIN) -->
            <?php if ($isLogin && $latestReview): ?>
                <div class="text-xs text-gray-300 mt-3 italic line-clamp-2">
                    "<?= htmlspecialchars(substr($latestReview['komentar'], 0, 70)) ?>..."
                </div>

                <a href="reviews.php?book_id=<?= $b['id'] ?>"
                   class="text-purple-400 text-xs underline mt-2 block hover:text-purple-300 transition">
                    Lihat Semua Ulasan →
                </a>
            <?php endif; ?>

            <!-- BUTTON -->
            <div class="mt-auto pt-6">
                <?php if (!$isLogin): ?>

                    <a href="login.php"
                       class="block text-center bg-purple-700 hover:bg-purple-800 text-white py-2 rounded-lg
                              font-semibold shadow-md transition-all duration-300 hover:-translate-y-1">
                        Register/Login untuk meminjam
                    </a>

                <?php elseif ($user['role'] !== 'peminjam'): ?>

                    <div class="text-center bg-gray-500 text-white py-2 rounded-lg cursor-not-allowed font-medium">
                        Admin/Petugas tidak bisa meminjam
                    </div>

                <?php else: ?>

                    <?php if (intval($b['stok']) <= 0): ?>
                        <div class="text-center bg-gray-700 text-gray-400 py-2 rounded-lg cursor-not-allowed font-medium">
                            Tidak Tersedia
                        </div>

                    <?php else: ?>
                        <a href="borrow.php?pinjam_buku=<?= $b['id'] ?>"
                           class="block text-center bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg 
                                  font-semibold shadow-lg transition-all duration-300 hover:-translate-y-1">
                            Pinjam
                        </a>
                    <?php endif; ?>

                <?php endif; ?>
            </div>

        </div>
        <?php endforeach; ?>

    </div>

    <?php endif; ?>

</div>

<?php require_once __DIR__.'/../templates/footer.php'; ?>
