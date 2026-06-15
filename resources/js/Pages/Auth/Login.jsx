import { useForm, Link } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

export default function Login() {
    const { data, setData, post, processing, errors } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/login');
    };

    return (
        <GuestLayout title="Login" description="Sign in to your account">
            <Card className="border-border">
                <form onSubmit={handleSubmit}>
                    <CardContent className="pt-6 space-y-4">
                        {/* Email */}
                        <div className="space-y-2">
                            <Label htmlFor="login-email">Email</Label>
                            <Input
                                id="login-email"
                                type="email"
                                placeholder="you@example.com"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                autoComplete="email"
                                autoFocus
                                required
                            />
                            {errors.email && (
                                <p className="text-xs text-destructive">{errors.email}</p>
                            )}
                        </div>

                        {/* Password */}
                        <div className="space-y-2">
                            <div className="flex items-center justify-between">
                                <Label htmlFor="login-password">Password</Label>
                                <Link
                                    href="/forgot-password"
                                    className="text-xs text-primary hover:text-primary/80 transition-colors"
                                >
                                    Forgot password?
                                </Link>
                            </div>
                            <Input
                                id="login-password"
                                type="password"
                                placeholder="Enter your password"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                autoComplete="current-password"
                                required
                            />
                            {errors.password && (
                                <p className="text-xs text-destructive">{errors.password}</p>
                            )}
                        </div>

                        {/* Remember me */}
                        <div className="flex items-center gap-2">
                            <input
                                id="remember-me"
                                type="checkbox"
                                className="h-4 w-4 rounded border-border bg-card text-primary focus:ring-primary"
                                checked={data.remember}
                                onChange={(e) => setData('remember', e.target.checked)}
                            />
                            <Label htmlFor="remember-me" className="text-sm text-muted-foreground cursor-pointer">
                                Remember me
                            </Label>
                        </div>
                    </CardContent>

                    <CardFooter className="flex flex-col gap-3">
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            id="login-submit"
                        >
                            {processing ? 'Signing in...' : 'Sign In'}
                        </Button>
                        <p className="text-sm text-muted-foreground">
                            Don't have an account?{' '}
                            <Link
                                href="/register"
                                className="text-primary hover:text-primary/80 font-medium transition-colors"
                            >
                                Register
                            </Link>
                        </p>
                    </CardFooter>
                </form>
            </Card>
        </GuestLayout>
    );
}
