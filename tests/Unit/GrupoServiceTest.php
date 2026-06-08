<?php

namespace Tests\Unit;

use App\Services\GrupoService;
use PHPUnit\Framework\TestCase;

/**
 * CU-11 · Prueba pura (sin BD) de la fórmula CantidadGrupos = ceil(total/capacidad).
 */
class GrupoServiceTest extends TestCase
{
    private GrupoService $svc;

    protected function setUp(): void
    {
        parent::setUp();
        $this->svc = new GrupoService();
    }

    public function test_calcula_cantidad_de_grupos_con_ceil(): void
    {
        $this->assertSame(0, $this->svc->calcularCantidadGrupos(0, 70));
        $this->assertSame(1, $this->svc->calcularCantidadGrupos(1, 70));
        $this->assertSame(1, $this->svc->calcularCantidadGrupos(70, 70));
        $this->assertSame(2, $this->svc->calcularCantidadGrupos(71, 70));
        $this->assertSame(3, $this->svc->calcularCantidadGrupos(150, 70)); // ceil(150/70)=3
    }

    public function test_capacidad_configurable(): void
    {
        $this->assertSame(2, $this->svc->calcularCantidadGrupos(150, 80)); // ceil(150/80)=2
        $this->assertSame(3, $this->svc->calcularCantidadGrupos(150, 60)); // ceil(150/60)=3
    }

    public function test_capacidad_cero_no_divide_por_cero(): void
    {
        $this->assertSame(0, $this->svc->calcularCantidadGrupos(100, 0));
    }
}
