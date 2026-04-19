Schema::create('seats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->integer('row_number');
            $table->integer('seat_number');
            $table->string('label')->unique();
            $table->boolean('is_vip_reserved')->default(false);
            $table->timestamps();

            $table->index(['section_id', 'row_number', 'seat_number']);
        });
