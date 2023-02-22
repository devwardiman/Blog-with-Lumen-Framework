<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    use HasFactory, HasUuids;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'user_id',
        'article_title',
        'article_abstract',
        'article_content',
        'article_cover',
        'article_feature',
        'article_status',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var string[]
     */
    protected $hidden = [];

    public function writer()
    {
        //return $this->hasMany(ArticleXCategory::class, 'article_id')->with(['category']);
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function category()
    {
        //return $this->hasMany(ArticleXCategory::class, 'article_id')->with(['category']);
        return $this->hasManyThrough(
            ArticleCategory::class,
            ArticleXCategory::class,
            'article_id',
            'id',
            'id',
            'article_category_id'
        );
    }

    public function comments()
    {
        return $this->hasMany(ArticleComment::class, 'article_id')->with(['replies', 'user' => function ($query) {
            $query->select('id', 'email', 'displayname');
        }]);
    }
}
