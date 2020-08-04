<?php
declare(strict_types = 1);

namespace RB\DB;

abstract class Migration
{
    public abstract function up(): void;

    public abstract function down(): void;
}