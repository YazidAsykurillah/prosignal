import { Link, usePage } from '@inertiajs/react';
import { LayoutDashboard, FolderKanban, Sparkles, Settings, X, Zap } from 'lucide-react';
import { cn } from '@/lib/utils';

const navigation = [
    { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard, active: true },
    { name: 'Projects', href: '/projects', icon: FolderKanban, active: true },
    { name: 'Opportunities', href: '#', icon: Sparkles, placeholder: true, tooltip: 'Coming in Sprint 3' },
    { name: 'Settings', href: '#', icon: Settings, placeholder: true, tooltip: 'Coming soon' },
];

export function Sidebar({ open, onClose }) {
    const { url } = usePage();

    return (
        <aside
            className={cn(
                "fixed inset-y-0 left-0 z-50 flex w-64 flex-col bg-sidebar border-r border-sidebar-border transition-transform duration-300 ease-in-out lg:static lg:translate-x-0",
                open ? "translate-x-0" : "-translate-x-full"
            )}
        >
            {/* Logo / Brand */}
            <div className="flex h-16 items-center gap-3 px-6 border-b border-sidebar-border">
                <div className="flex h-9 w-9 items-center justify-center rounded-lg bg-primary">
                    <Zap className="h-5 w-5 text-primary-foreground" />
                </div>
                <div className="flex flex-col">
                    <span className="text-sm font-bold text-sidebar-primary-foreground tracking-tight">
                        Signal Finder
                    </span>
                    <span className="text-[10px] font-medium text-sidebar-foreground/50 uppercase tracking-widest">
                        Intelligence
                    </span>
                </div>

                {/* Close button (mobile) */}
                <button
                    onClick={onClose}
                    className="ml-auto rounded-md p-1 text-sidebar-foreground hover:bg-sidebar-accent lg:hidden transition-colors"
                    aria-label="Close sidebar"
                >
                    <X className="h-5 w-5" />
                </button>
            </div>

            {/* Navigation */}
            <nav className="flex-1 px-3 py-4 space-y-1">
                <p className="px-3 mb-3 text-[11px] font-semibold uppercase tracking-wider text-sidebar-foreground/40">
                    Navigation
                </p>
                {navigation.map((item) => {
                    const isActive = !item.placeholder && url.startsWith(item.href);
                    const Icon = item.icon;

                    if (item.placeholder) {
                        return (
                            <div
                                key={item.name}
                                className="group relative flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium text-sidebar-foreground/30 cursor-not-allowed"
                                title={item.tooltip}
                            >
                                <Icon className="h-4.5 w-4.5" />
                                <span>{item.name}</span>
                                <span className="ml-auto text-[10px] font-medium bg-sidebar-accent/50 text-sidebar-foreground/40 px-1.5 py-0.5 rounded">
                                    Soon
                                </span>
                            </div>
                        );
                    }

                    return (
                        <Link
                            key={item.name}
                            href={item.href}
                            className={cn(
                                "flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition-all duration-200",
                                isActive
                                    ? "bg-sidebar-primary/15 text-sidebar-primary shadow-sm"
                                    : "text-sidebar-foreground hover:bg-sidebar-accent hover:text-sidebar-accent-foreground"
                            )}
                        >
                            <Icon className={cn("h-4.5 w-4.5", isActive && "text-sidebar-primary")} />
                            <span>{item.name}</span>
                            {isActive && (
                                <div className="ml-auto h-1.5 w-1.5 rounded-full bg-sidebar-primary shadow-sm shadow-primary/50" />
                            )}
                        </Link>
                    );
                })}
            </nav>

            {/* Footer */}
            <div className="border-t border-sidebar-border px-4 py-3">
                <div className="flex items-center gap-2 text-[11px] text-sidebar-foreground/30">
                    <div className="h-1.5 w-1.5 rounded-full bg-success animate-pulse" />
                    <span>System Online</span>
                </div>
            </div>
        </aside>
    );
}
