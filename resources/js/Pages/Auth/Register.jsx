import { useForm, Link } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

export default function Register() {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/register');
    };

    return (
        <GuestLayout title="Register" description="Create your account">
            <Card className="border-border">
                <form onSubmit={handleSubmit}>
                    <CardContent className="pt-6 space-y-4">
                        {/* Name */}
                        <div className="space-y-2">
                            <Label htmlFor="register-name">Full Name</Label>
                            <Input
                                id="register-name"
                                type="text"
                                placeholder="John Doe"
                                value={data.name}
                                onChange={(e) => setData('name', e.target.value)}
                                autoComplete="name"
                                autoFocus
                                required
                            />
                            {errors.name && (
                                <p className="text-xs text-destructive">{errors.name}</p>
                            )}
                        </div>

                        {/* Email */}
                        <div className="space-y-2">
                            <Label htmlFor="register-email">Email</Label>
                            <Input
                                id="register-email"
                                type="email"
                                placeholder="you@example.com"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                autoComplete="email"
                                required
                            />
                            {errors.email && (
                                <p className="text-xs text-destructive">{errors.email}</p>
                            )}
                        </div>

                        {/* Password */}
                        <div className="space-y-2">
                            <Label htmlFor="register-password">Password</Label>
                            <Input
                                id="register-password"
                                type="password"
                                placeholder="Minimum 12 characters"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                autoComplete="new-password"
                                required
                            />
                            {errors.password && (
                                <p className="text-xs text-destructive">{errors.password}</p>
                            )}
                        </div>

                        {/* Confirm Password */}
                        <div className="space-y-2">
                            <Label htmlFor="register-password-confirmation">Confirm Password</Label>
                            <Input
                                id="register-password-confirmation"
                                type="password"
                                placeholder="Repeat your password"
                                value={data.password_confirmation}
                                onChange={(e) => setData('password_confirmation', e.target.value)}
                                autoComplete="new-password"
                                required
                            />
                            {errors.password_confirmation && (
                                <p className="text-xs text-destructive">{errors.password_confirmation}</p>
                            )}
                        </div>
                    </CardContent>

                    <CardFooter className="flex flex-col gap-3">
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            id="register-submit"
                        >
                            {processing ? 'Creating account...' : 'Create Account'}
                        </Button>
                        <p className="text-sm text-muted-foreground">
                            Already have an account?{' '}
                            <Link
                                href="/login"
                                className="text-primary hover:text-primary/80 font-medium transition-colors"
                            >
                                Sign in
                            </Link>
                        </p>
                    </CardFooter>
                </form>
            </Card>
        </GuestLayout>
    );
}
