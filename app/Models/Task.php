<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;


class Task extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'descricao',
        'tarefa_processadora',
        'status',
        'data_inicial',
        'data_final',
        'projeto_id'
    ];
}
