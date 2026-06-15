import { Head, Link } from '@inertiajs/react';
import { Zap } from 'lucide-react';

export default function GuestLayout({ children, title, description }) {
    return (
        <>
            <Head title={title} />
            <div className="flex min-h-screen items-center justify-center bg-background p-4">

                <div className="relative w-full max-w-md">
                    {/* Logo */}
                    <div className="mb-8 flex flex-col items-center">
                        <div className="flex h-12 w-12 items-center justify-center rounded-xl bg-primary text-primary-foreground mb-4">
                            <Zap className="h-6 w-6 text-primary-foreground" />
                        </div>
                        <h1 className="text-xl font-bold tracking-tight text-foreground">
                            Signal Finder
                        </h1>
                        {description && (
                            <p className="mt-1 text-sm text-muted-foreground text-center">
                                {description}
                            </p>
                        )}
                    </div>

                    {/* Content */}
                    {children}

                    {/* Footer */}
                    <p className="mt-6 text-center text-xs text-muted-foreground/50">
                        Project Signal Intelligence
                    </p>
                </div>
            </div>
        </>
    );
}
