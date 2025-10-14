<?php

namespace App\Repositories;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Repository base che fornisce operazioni CRUD comuni.
 * Tutti i repository specifici estendono questa classe.
 */
abstract class BaseRepository
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Ottiene tutti i record.
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Trova un record per ID.
     */
    public function findById(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Trova un record per ID o lancia un'eccezione.
     */
    public function findByIdOrFail(int $id): Model
    {
        return $this->model->findOrFail($id);
    }

    /**
     * Crea un nuovo record.
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Aggiorna un record esistente.
     */
    public function update(int $id, array $data): bool
    {
        $record = $this->findByIdOrFail($id);
        return $record->update($data);
    }

    /**
     * Elimina un record.
     */
    public function delete(int $id): bool
    {
        $record = $this->findByIdOrFail($id);
        return $record->delete();
    }

    /**
     * Paginazione dei risultati.
     */
    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    /**
     * Conta il numero totale di record.
     */
    public function count(): int
    {
        return $this->model->count();
    }
}
