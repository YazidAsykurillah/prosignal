import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription, CardFooter } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { Input } from '@/Components/ui/input';
import { Label } from '@/Components/ui/label';
import { ArrowLeft, Save } from 'lucide-react';

export default function Create({ auth }) {
    const { data, setData, post, processing, errors } = useForm({
        name: '',
        description: '',
        industry: '',
        location: '',
    });

    const submit = (e) => {
        e.preventDefault();
        post('/projects');
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Create Project" />

            <div className="mb-6 flex items-center gap-4">
                <Button variant="ghost" size="icon" asChild className="rounded-full">
                    <Link href="/projects">
                        <ArrowLeft className="h-5 w-5" />
                        <span className="sr-only">Back to projects</span>
                    </Link>
                </Button>
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-foreground lg:text-3xl">
                        Create Project
                    </h1>
                </div>
            </div>

            <div className="max-w-2xl">
                <form onSubmit={submit}>
                    <Card>
                        <CardHeader>
                            <CardTitle>Project Details</CardTitle>
                            <CardDescription>
                                Provide details about your business or the product you are selling. This will be used by our AI to find relevant opportunities.
                            </CardDescription>
                        </CardHeader>
                        <CardContent className="space-y-6">
                            <div className="space-y-2">
                                <Label htmlFor="name">Project Name <span className="text-destructive">*</span></Label>
                                <Input
                                    id="name"
                                    value={data.name}
                                    onChange={(e) => setData('name', e.target.value)}
                                    placeholder="e.g. ERP Software Sales"
                                    required
                                />
                                {errors.name && <p className="text-sm text-destructive mt-1">{errors.name}</p>}
                            </div>

                            <div className="space-y-2">
                                <Label htmlFor="description">Business Description</Label>
                                <textarea
                                    id="description"
                                    className="flex min-h-[120px] w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-sm placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring disabled:cursor-not-allowed disabled:opacity-50"
                                    value={data.description}
                                    onChange={(e) => setData('description', e.target.value)}
                                    placeholder="Describe what you sell or offer. e.g. Saya menjual software MES untuk manufaktur."
                                />
                                {errors.description && <p className="text-sm text-destructive mt-1">{errors.description}</p>}
                            </div>

                            <div className="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                <div className="space-y-2">
                                    <Label htmlFor="industry">Target Industry</Label>
                                    <Input
                                        id="industry"
                                        value={data.industry}
                                        onChange={(e) => setData('industry', e.target.value)}
                                        placeholder="e.g. Manufacturing, IT"
                                    />
                                    {errors.industry && <p className="text-sm text-destructive mt-1">{errors.industry}</p>}
                                </div>

                                <div className="space-y-2">
                                    <Label htmlFor="location">Target Location</Label>
                                    <Input
                                        id="location"
                                        value={data.location}
                                        onChange={(e) => setData('location', e.target.value)}
                                        placeholder="e.g. Indonesia, Jakarta"
                                    />
                                    {errors.location && <p className="text-sm text-destructive mt-1">{errors.location}</p>}
                                </div>
                            </div>
                        </CardContent>
                        <CardFooter className="flex justify-end gap-3 border-t bg-muted/20 px-6 py-4">
                            <Button variant="outline" asChild>
                                <Link href="/projects">Cancel</Link>
                            </Button>
                            <Button type="submit" disabled={processing} className="gap-2">
                                <Save className="h-4 w-4" />
                                {processing ? 'Creating...' : 'Create Project'}
                            </Button>
                        </CardFooter>
                    </Card>
                </form>
            </div>
        </AuthenticatedLayout>
    );
}
