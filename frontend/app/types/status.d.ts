export interface SystemStatus {
    title: string;
    database_status: string;
    database_version: string;
    redis_status: 'Online' | 'Offline' | 'Disconnected (Check Sail)';
    server_time: string;
    php_version: string;
}