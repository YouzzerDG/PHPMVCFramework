<?php namespace Controller;

interface IController {
    public function index(): void;
    public function show($id): void;
    public function create(): void;
    public function update($id): void;
    public function delete($id): void;
}