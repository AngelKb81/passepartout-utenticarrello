<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'display_name',
        'description',
    ];

    /**
     * Relazione many-to-many con gli utenti.
     * Un ruolo può essere assegnato a più utenti.
     */
    public function users()
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    /**
     * Scope per ottenere un ruolo per nome.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }
}
