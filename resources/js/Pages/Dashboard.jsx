import { Head, usePage } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle } from '@/Components/ui/card';
import { Users, Activity, Zap, Server } from 'lucide-react';

export default function Dashboard() {
    const { auth } = usePage().props;
    const user = auth.user;

    const stats = [
        {
            title: 'System Status',
            value: 'Operational',
            description: 'All services running',
            icon: Server,
            color: 'text-success',
            bgColor: 'bg-success/10',
        },
        {
            title: 'Queue Status',
            value: 'Active',
            description: 'Horizon monitoring',
            icon: Zap,
            color: 'text-primary',
            bgColor: 'bg-primary/10',
        },
        {
            title: 'Your Role',
            value: user?.roles?.[0] || 'Member',
            description: 'Current access level',
            icon: Users,
            color: 'text-warning',
            bgColor: 'bg-warning/10',
        },
        {
            title: 'Activity',
            value: 'Tracked',
            description: 'Audit log enabled',
            icon: Activity,
            color: 'text-info',
            bgColor: 'bg-info/10',
        },
    ];

    return (
        <AuthenticatedLayout user={user}>
            <Head title="Dashboard" />

            {/* Welcome section */}
            <div className="mb-8">
                <h1 className="text-2xl font-bold tracking-tight text-foreground lg:text-3xl">
                    Welcome back, {user?.name}
                </h1>
                <p className="mt-1 text-muted-foreground">
                    Project Signal Intelligence — Foundation is ready.
                </p>
            </div>

            {/* Status cards */}
            <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {stats.map((stat) => {
                    const Icon = stat.icon;
                    return (
                        <Card
                            key={stat.title}
                            className="group hover:border-primary/50 transition-colors duration-200"
                        >
                            <CardHeader className="flex flex-row items-center justify-between pb-2">
                                <CardTitle className="text-sm font-medium text-muted-foreground">
                                    {stat.title}
                                </CardTitle>
                                <div className={`rounded-lg p-2 ${stat.bgColor} transition-colors duration-200 group-hover:bg-primary/20`}>
                                    <Icon className={`h-4 w-4 ${stat.color}`} />
                                </div>
                            </CardHeader>
                            <CardContent>
                                <div className="text-xl font-bold">{stat.value}</div>
                                <p className="text-xs text-muted-foreground mt-1">
                                    {stat.description}
                                </p>
                            </CardContent>
                        </Card>
                    );
                })}
            </div>

            {/* Getting Started section */}
            <div className="mt-8">
                <Card className="border-dashed border-border/50">
                    <CardHeader>
                        <CardTitle className="text-lg">Sprint 0 Complete ✓</CardTitle>
                    </CardHeader>
                    <CardContent>
                        <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
                            {[
                                '✅ Docker Environment',
                                '✅ Laravel 12 + React/Inertia',
                                '✅ Authentication System',
                                '✅ RBAC (Spatie Permission)',
                                '✅ Dashboard Foundation',
                                '✅ Queue + Horizon',
                                '✅ Activity Logging',
                                '✅ Mailpit Integration',
                                '✅ Backup Configuration',
                            ].map((item) => (
                                <div
                                    key={item}
                                    className="flex items-center gap-2 rounded-lg bg-card/50 px-3 py-2 text-sm text-muted-foreground"
                                >
                                    {item}
                                </div>
                            ))}
                        </div>
                        <p className="mt-4 text-sm text-muted-foreground">
                            Ready for <span className="font-semibold text-primary">Sprint 1 — Project Management</span>
                        </p>
                    </CardContent>
                </Card>
            </div>
        </AuthenticatedLayout>
    );
}
