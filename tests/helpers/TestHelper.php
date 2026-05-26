<?php
/**
 * MANÁ FAST FOOD - TEST HELPER
 * 
 * Clase con métodos utilitarios para los tests.
 * NO usar herencia directa, sino llamar a los métodos estáticos.
 */

class TestHelper
{
    /**
     * Reinicia el singleton de Database para poder inyectar un mock
     * 
     * @param PDO|null $mockPdo Mock de PDO o null para resetear
     */
    public static function resetDatabase(?PDO $mockPdo = null): void
    {
        // Resetear el singleton
        Database::reset();
        
        // Reflejar para reemplazar la instancia
        if ($mockPdo !== null) {
            $reflection = new ReflectionClass(Database::class);
            $instanceProp = $reflection->getProperty('instance');
            $instanceProp->setAccessible(true);
            $instanceProp->setValue(null, $mockPdo);
        }
    }
    
    /**
     * Crea un mock de PDOStatement con resultados configurables
     * 
     * @param array $fetchResult Resultado de fetch()
     * @param array $fetchAllResult Resultado de fetchAll()
     * @param int $rowCount Número de filas afectadas
     * @return PHPUnit\Framework\MockObject\MockObject
     */
    public static function createMockStatement($testCase, array $fetchResult = [], array $fetchAllResult = [], int $rowCount = 1)
    {
        $mockStmt = $testCase->createMock(PDOStatement::class);
        $mockStmt->method('execute')->willReturn(true);
        $mockStmt->method('fetch')->willReturn($fetchResult ?: false);
        $mockStmt->method('fetchAll')->willReturn($fetchAllResult);
        $mockStmt->method('rowCount')->willReturn($rowCount);
        $mockStmt->method('closeCursor')->willReturn(true);
        
        return $mockStmt;
    }
    
    /**
     * Crea un mock de PDO completo
     * 
     * @param mixed $testCase Instancia del test case
     * @param array $methodsConfig Configuración de métodos
     * @return PHPUnit\Framework\MockObject\MockObject
     */
    public static function createMockPDO($testCase, array $methodsConfig = [])
    {
        $defaultStmt = self::createMockStatement($testCase);
        
        $mockPdo = $testCase->createMock(PDO::class);
        $mockPdo->method('prepare')->willReturn($methodsConfig['prepare'] ?? $defaultStmt);
        $mockPdo->method('query')->willReturn($methodsConfig['query'] ?? $defaultStmt);
        $mockPdo->method('lastInsertId')->willReturn($methodsConfig['lastInsertId'] ?? '1');
        $mockPdo->method('beginTransaction')->willReturn(true);
        $mockPdo->method('commit')->willReturn(true);
        $mockPdo->method('rollBack')->willReturn(true);
        $mockPdo->method('exec')->willReturn($methodsConfig['exec'] ?? 1);
        $mockPdo->method('quote')->willReturnCallback(function ($str) {
            return "'" . addslashes($str) . "'";
        });
        
        return $mockPdo;
    }
    
    /**
     * Simula el body de una petición HTTP
     * 
     * @param array $data Datos a simular
     */
    public static function setRequestBody(array $data): void
    {
        $GLOBALS['_TEST_INPUT'] = json_encode($data);
    }
    
    /**
     * Simula headers HTTP
     * 
     * @param string $key Nombre del header
     * @param string $value Valor del header
     */
    public static function setRequestHeader(string $key, string $value): void
    {
        $_SERVER['HTTP_' . strtoupper(str_replace('-', '_', $key))] = $value;
    }
    
    /**
     * Obtiene datos de fixtures
     * 
     * @param string $key Clave del fixture
     * @return mixed
     */
    public static function fixture(string $key)
    {
        $fixtures = require __DIR__ . '/../fixtures/data.php';
        return $fixtures[$key] ?? null;
    }
    
    /**
     * Crea un archivo de imagen temporal para tests
     */
    public static function createTempImage(string $extension = 'jpg'): string
    {
        return createTestImageFile($extension);
    }
}
