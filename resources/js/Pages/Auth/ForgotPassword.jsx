import { useForm, Link } from '@inertiajs/react';
import GuestLayout from '@/Layouts/GuestLayout';
import { Card, CardContent, CardFooter } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { usePage } from '@inertiajs/react';

export default function ForgotPassword() {
    const { flash } = usePage().props;
    const { data, setData, post, processing, errors } = useForm({
        email: '',
    });

    const handleSubmit = (e) => {
        e.preventDefault();
        post('/forgot-password');
    };

    return (
        <GuestLayout title="Forgot Password" description="Enter your email to receive a reset link">
            <Card className="border-border">
                <form onSubmit={handleSubmit}>
                    <CardContent className="pt-6 space-y-4">
                        {/* Success message */}
                        {flash?.success && (
                            <div className="rounded-lg bg-success/10 border border-success/20 px-4 py-3 text-sm text-success">
                                {flash.success}
                            </div>
                        )}

                        {/* Email */}
                        <div className="space-y-2">
                            <Label htmlFor="forgot-email">Email Address</Label>
                            <Input
                                id="forgot-email"
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
                    </CardContent>

                    <CardFooter className="flex flex-col gap-3">
                        <Button
                            type="submit"
                            className="w-full"
                            disabled={processing}
                            id="forgot-password-submit"
                        >
                            {processing ? 'Sending...' : 'Send Reset Link'}
                        </Button>
                        <Link
                            href="/login"
                            className="text-sm text-muted-foreground hover:text-foreground transition-colors"
                        >
                            ← Back to login
                        </Link>
                    </CardFooter>
                </form>
            </Card>
        </GuestLayout>
    );
}
