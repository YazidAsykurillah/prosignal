import { router } from '@inertiajs/react';
import { Menu, LogOut, User, ChevronDown } from 'lucide-react';
import { Avatar, AvatarFallback } from '@/Components/ui/avatar';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuLabel,
    DropdownMenuSeparator,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';

export function Header({ user, onMenuClick }) {
    const initials = user?.name
        ? user.name.split(' ').map((n) => n[0]).join('').toUpperCase().slice(0, 2)
        : '??';

    const primaryRole = user?.roles?.[0] || 'Member';

    const handleLogout = () => {
        router.post('/logout');
    };

    return (
        <header className="sticky top-0 z-30 flex h-16 items-center gap-4 border-b border-border bg-background px-4 lg:px-8">
            {/* Mobile menu button */}
            <button
                onClick={onMenuClick}
                className="rounded-lg p-2 text-muted-foreground hover:bg-accent hover:text-foreground transition-colors lg:hidden"
                aria-label="Open sidebar"
            >
                <Menu className="h-5 w-5" />
            </button>

            {/* Spacer */}
            <div className="flex-1" />

            {/* User menu */}
            <DropdownMenu>
                <DropdownMenuTrigger asChild>
                    <button
                        className="flex items-center gap-3 rounded-xl px-3 py-2 hover:bg-accent transition-colors duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-ring"
                        id="user-menu-button"
                    >
                        <div className="hidden sm:flex flex-col items-end">
                            <span className="text-sm font-medium text-foreground">
                                {user?.name}
                            </span>
                            <span className="text-[11px] font-medium text-primary/80">
                                {primaryRole}
                            </span>
                        </div>
                        <Avatar className="h-8 w-8">
                            <AvatarFallback>{initials}</AvatarFallback>
                        </Avatar>
                        <ChevronDown className="h-4 w-4 text-muted-foreground hidden sm:block" />
                    </button>
                </DropdownMenuTrigger>

                <DropdownMenuContent align="end" className="w-56">
                    <DropdownMenuLabel className="font-normal">
                        <div className="flex flex-col space-y-1">
                            <p className="text-sm font-medium">{user?.name}</p>
                            <p className="text-xs text-muted-foreground">{user?.email}</p>
                        </div>
                    </DropdownMenuLabel>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        className="cursor-pointer"
                        onClick={() => {/* TODO: Profile page */}}
                    >
                        <User className="mr-2 h-4 w-4" />
                        <span>Profile</span>
                    </DropdownMenuItem>
                    <DropdownMenuSeparator />
                    <DropdownMenuItem
                        className="cursor-pointer text-destructive focus:text-destructive"
                        onClick={handleLogout}
                    >
                        <LogOut className="mr-2 h-4 w-4" />
                        <span>Logout</span>
                    </DropdownMenuItem>
                </DropdownMenuContent>
            </DropdownMenu>
        </header>
    );
}
