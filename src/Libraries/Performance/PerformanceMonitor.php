<?php

namespace Miladshm\ControllerHelpers\Libraries\Performance;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PerformanceMonitor
{
    private float $startTime;
    private int $startMemory;
    private int $startQueries;
    private array $metrics = [];
    private bool $enabled;

    public function __construct(bool $enabled = null)
    {
        $this->enabled = $enabled ?? config('controller-helpers.performance.enable_performance_metrics', false);
    }

    /**
     * Start monitoring performance
     */
    public function start(string $operation = 'default'): self
    {
        if (!$this->enabled) {
            return $this;
        }

        $this->startTime = microtime(true);
        $this->startMemory = memory_get_usage(true);
        $this->startQueries = $this->getQueryCount();
        
        $this->metrics[$operation] = [
            'start_time' => $this->startTime,
            'start_memory' => $this->startMemory,
            'start_queries' => $this->startQueries,
        ];

        return $this;
    }

    /**
     * Stop monitoring and collect metrics
     */
    public function stop(string $operation = 'default'): array
    {
        if (!$this->enabled || !isset($this->metrics[$operation])) {
            return [];
        }

        $endTime = microtime(true);
        $endMemory = memory_get_usage(true);
        $endQueries = $this->getQueryCount();

        $metrics = [
            'operation' => $operation,
            'execution_time_ms' => round(($endTime - $this->metrics[$operation]['start_time']) * 1000, 2),
            'memory_used_bytes' => $endMemory - $this->metrics[$operation]['start_memory'],
            'memory_used_formatted' => $this->formatBytes($endMemory - $this->metrics[$operation]['start_memory']),
            'memory_peak_bytes' => memory_get_peak_usage(true),
            'memory_peak_formatted' => $this->formatBytes(memory_get_peak_usage(true)),
            'queries_executed' => $endQueries - $this->metrics[$operation]['start_queries'],
            'timestamp' => now()->toISOString(),
        ];

        // Log metrics if enabled
        if (config('controller-helpers.performance.log_metrics', false)) {
            Log::info('Performance Metrics', $metrics);
        }

        unset($this->metrics[$operation]);

        return $metrics;
    }

    /**
     * Monitor a callable and return metrics
     */
    public function monitor(callable $callback, string $operation = 'monitored_operation'): array
    {
        $this->start($operation);
        
        try {
            $result = $callback();
            $metrics = $this->stop($operation);
            $metrics['success'] = true;
            $metrics['result'] = $result;
            return $metrics;
        } catch (\Exception $e) {
            $metrics = $this->stop($operation);
            $metrics['success'] = false;
            $metrics['error'] = $e->getMessage();
            throw $e;
        }
    }

    /**
     * Get current query count
     */
    private function getQueryCount(): int
    {
        return count(DB::getQueryLog());
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, $precision) . ' ' . $units[$i];
    }

    /**
     * Get comprehensive system metrics
     */
    public function getSystemMetrics(): array
    {
        return [
            'php_version' => PHP_VERSION,
            'memory_limit' => ini_get('memory_limit'),
            'memory_current' => $this->formatBytes(memory_get_usage(true)),
            'memory_peak' => $this->formatBytes(memory_get_peak_usage(true)),
            'time_limit' => ini_get('max_execution_time'),
            'opcache_enabled' => extension_loaded('opcache') && opcache_get_status()['opcache_enabled'] ?? false,
            'query_logging_enabled' => config('database.connections.' . config('database.default') . '.options.PDO::MYSQL_ATTR_USE_BUFFERED_QUERY', false),
        ];
    }

    /**
     * Generate performance report
     */
    public function generateReport(array $metrics): string
    {
        $report = "Performance Report\n";
        $report .= "==================\n\n";
        
        foreach ($metrics as $key => $value) {
            if (is_array($value)) {
                $report .= "{$key}:\n";
                foreach ($value as $subKey => $subValue) {
                    $report .= "  {$subKey}: {$subValue}\n";
                }
            } else {
                $report .= "{$key}: {$value}\n";
            }
        }
        
        return $report;
    }

    /**
     * Compare performance metrics
     */
    public function compareMetrics(array $before, array $after): array
    {
        $comparison = [];
        
        if (isset($before['execution_time_ms']) && isset($after['execution_time_ms'])) {
            $timeDiff = $after['execution_time_ms'] - $before['execution_time_ms'];
            $timeImprovement = $before['execution_time_ms'] > 0 ? 
                round((($before['execution_time_ms'] - $after['execution_time_ms']) / $before['execution_time_ms']) * 100, 2) : 0;
            
            $comparison['execution_time'] = [
                'before' => $before['execution_time_ms'] . 'ms',
                'after' => $after['execution_time_ms'] . 'ms',
                'difference' => $timeDiff . 'ms',
                'improvement_percent' => $timeImprovement . '%'
            ];
        }
        
        if (isset($before['memory_used_bytes']) && isset($after['memory_used_bytes'])) {
            $memoryDiff = $after['memory_used_bytes'] - $before['memory_used_bytes'];
            $memoryImprovement = $before['memory_used_bytes'] > 0 ? 
                round((($before['memory_used_bytes'] - $after['memory_used_bytes']) / $before['memory_used_bytes']) * 100, 2) : 0;
            
            $comparison['memory_usage'] = [
                'before' => $this->formatBytes($before['memory_used_bytes']),
                'after' => $this->formatBytes($after['memory_used_bytes']),
                'difference' => $this->formatBytes(abs($memoryDiff)) . ($memoryDiff > 0 ? ' increase' : ' decrease'),
                'improvement_percent' => $memoryImprovement . '%'
            ];
        }
        
        if (isset($before['queries_executed']) && isset($after['queries_executed'])) {
            $queryDiff = $after['queries_executed'] - $before['queries_executed'];
            $queryImprovement = $before['queries_executed'] > 0 ? 
                round((($before['queries_executed'] - $after['queries_executed']) / $before['queries_executed']) * 100, 2) : 0;
            
            $comparison['query_count'] = [
                'before' => $before['queries_executed'],
                'after' => $after['queries_executed'],
                'difference' => $queryDiff,
                'improvement_percent' => $queryImprovement . '%'
            ];
        }
        
        return $comparison;
    }

    /**
     * Enable or disable monitoring
     */
    public function setEnabled(bool $enabled): self
    {
        $this->enabled = $enabled;
        return $this;
    }

    /**
     * Check if monitoring is enabled
     */
    public function isEnabled(): bool
    {
        return $this->enabled;
    }
}