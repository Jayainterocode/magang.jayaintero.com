<?php

namespace App\Enums;

enum EducationLevel: string
{
    case Smk = 'smk';
    case D3 = 'd3';
    case D4 = 'd4';
    case S1 = 's1';

    public function label(): string
    {
        return match ($this) {
            self::Smk => 'SMK',
            self::D3 => 'D3',
            self::D4 => 'D4',
            self::S1 => 'S1',
        };
    }

    /**
     * @return list<array{value: string, label: string}>
     */
    public static function options(): array
    {
        return array_map(
            fn (self $level): array => ['value' => $level->value, 'label' => $level->label()],
            self::cases(),
        );
    }
}
