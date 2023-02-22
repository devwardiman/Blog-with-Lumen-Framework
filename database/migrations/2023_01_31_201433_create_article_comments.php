<?php

use App\Models\Article;
use App\Models\ArticleComment;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('article_comments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(User::class)->constrained()->onUpdate("cascade")->onDelete("cascade");
            $table->foreignIdFor(Article::class)->constrained()->onUpdate("cascade")->onDelete("cascade");
            $table->longText('comment_content');
            $table->timestamps();
        });

        Schema::create('article_replies', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(User::class)->constrained()->onUpdate("cascade")->onDelete("cascade");
            $table->foreignIdFor(Article::class)->constrained()->onUpdate("cascade")->onDelete("cascade");
            $table->foreignIdFor(ArticleComment::class)->nullable()->constrained()->onUpdate("cascade")->onDelete("cascade");
            $table->longText('comment_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('article_comments');
        Schema::dropIfExists('article_replies');
    }
};
