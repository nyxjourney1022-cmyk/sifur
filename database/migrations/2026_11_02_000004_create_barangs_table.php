    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('barangs', function (Blueprint $table) {
                $table->id();
                $table->string('nama_barang');
                $table->integer('stok_barang');
                $table->decimal('harga', 12, 2);
                $table->foreignId('kayu_id')->constrained('kayus');
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('barangs');
        }
    };
