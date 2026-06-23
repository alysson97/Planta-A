<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // ENUM em vez de tabela `lines` separada: o enunciado fixa exatamente
        // 4 linhas de produto, sem indicação de cadastro dinâmico. ENUM evita
        // um JOIN apenas para exibir um rótulo e ainda funciona como
        // constraint de integridade no próprio banco. Trade-off aceito: se a
        // fábrica vier a adicionar uma 5ª linha, exige uma migration.
        Schema::create('productions', function (Blueprint $table) {
            $table->id();
            $table->enum('line', ['Geladeira', 'Máquina de Lavar', 'TV', 'Ar-Condicionado']);
            $table->date('production_date');
            $table->unsignedInteger('produced');
            $table->unsignedInteger('defects');
            $table->timestamps();

            // Índice composto: cobre o recorte por mês (production_date) e
            // o GROUP BY line já com a coluna de filtro à frente.
            $table->index(['production_date', 'line']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('productions');
    }
}
