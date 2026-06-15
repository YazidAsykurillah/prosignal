import { useForm } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';

export default function ResetPassword({ token, email }) {
    const { data, setData, post, processing, errors } = useForm({
        token: token,
        email: email || '',
        password: '',
        password_confirmation: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/reset-password');
    };

    return (
        <GuestLayout title="Reset Password" description="Choose a new password for your account">
            <Card className="border-border">
                <form onSubmit={handleSubmit}>
                    <CardContent className="pt-6 space-y-4">
                        {/* Email (prefilled, readonly) */}
                        <div className="space-y-2">
                            <Label htmlFor="reset-email">Email</Label>
                            <Input
                                id="reset-email"
                                type="email"
                                value={data.email}
                                onChange={(e) => setData('email', e.target.value)}
                                autoComplete="email"
                                required
                            />
                            {errors.email && (
                                <p className="text-xs text-destructive">{errors.email}</p>
                            )}
                        </div>

                        {/* New Password */}
                        <div className="space-y-2">
                            <Label htmlFor="reset-password">New Password</Label>
                            <Input
                                id="reset-password"
                                type="password"
                                placeholder="Minimum 12 characters"
                                value={data.password}
                                onChange={(e) => setData('password', e.target.value)}
                                autoComplete="new-password"
                                autoFocus
                                required
                            />
                            {errors.password && (
                                <p className="text-xs text-destructive">{errors.password}</p>
                            )}
                        </div>

                        {/* Confirm Password */}
                        <div className="space-y-2">
                            <Label htmlFor="reset-password-confirmation">Confirm Password</Label>
                            <Input
                                id="reset-password-confirmation"
                                type="password"
                                placeholder="Repeat your new password"
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

                    <CardFooter>
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            id="reset-password-submit"
                        >
                            {processing ? 'Resetting...' : 'Reset Password'}
                        </Button>
                    </CardFooter>
                </form>
            </Card>
        </GuestLayout>
    );
}
