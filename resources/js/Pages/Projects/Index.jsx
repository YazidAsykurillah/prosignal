import { Head, Link, useForm } from '@inertiajs/react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Card, CardContent, CardHeader, CardTitle, CardDescription } from '@/Components/ui/card';
import { Button } from '@/Components/ui/button';
import { FolderKanban, Plus, MoreVertical, Pencil, Trash2, MapPin, Building2 } from 'lucide-react';
import {
    DropdownMenu,
    DropdownMenuContent,
    DropdownMenuItem,
    DropdownMenuTrigger,
} from '@/Components/ui/dropdown-menu';

export default function Index({ auth, projects }) {
    const { delete: destroy } = useForm();

    const handleDelete = (id) => {
        if (confirm('Are you sure you want to delete this project?')) {
            destroy(`/projects/${id}`);
        }
    };

    return (
        <AuthenticatedLayout user={auth.user}>
            <Head title="Projects" />

            <div className="mb-8 flex items-center justify-between">
                <div>
                    <h1 className="text-2xl font-bold tracking-tight text-foreground lg:text-3xl">
                        Projects
                    </h1>
                    <p className="mt-1 text-muted-foreground">
                        Manage your target projects and opportunities.
                    </p>
                </div>
                <Button asChild>
                    <Link href="/projects/create" className="gap-2">
                        <Plus className="h-4 w-4" />
                        New Project
                    </Link>
                </Button>
            </div>

            {projects.data.length === 0 ? (
                <Card className="border-dashed border-border/50 bg-transparent flex flex-col items-center justify-center p-12 text-center">
                    <div className="rounded-full bg-primary/10 p-4 mb-4 text-primary">
                        <FolderKanban className="h-8 w-8" />
                    </div>
                    <h3 className="text-lg font-semibold mb-2">No projects yet</h3>
                    <p className="text-sm text-muted-foreground mb-6 max-w-sm">
                        Create your first project to start finding opportunities, leads, and generating AI insights.
                    </p>
                    <Button asChild>
                        <Link href="/projects/create" className="gap-2">
                            <Plus className="h-4 w-4" />
                            Create Project
                        </Link>
                    </Button>
                </Card>
            ) : (
                <div className="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    {projects.data.map((project) => (
                        <Card key={project.id} className="group hover:border-primary/50 transition-all duration-200 hover:shadow-md">
                            <CardHeader className="flex flex-row items-start justify-between pb-2 space-y-0">
                                <div>
                                    <CardTitle className="text-lg leading-tight group-hover:text-primary transition-colors">
                                        <Link href={`/projects/${project.id}`}>
                                            {project.name}
                                        </Link>
                                    </CardTitle>
                                </div>
                                <DropdownMenu>
                                    <DropdownMenuTrigger asChild>
                                        <Button variant="ghost" size="icon" className="h-8 w-8 -mr-2 text-muted-foreground hover:text-foreground">
                                            <MoreVertical className="h-4 w-4" />
                                            <span className="sr-only">Open menu</span>
                                        </Button>
                                    </DropdownMenuTrigger>
                                    <DropdownMenuContent align="end" className="w-[160px]">
                                        <DropdownMenuItem asChild className="cursor-pointer">
                                            <Link href={`/projects/${project.id}/edit`} className="flex w-full items-center">
                                                <Pencil className="mr-2 h-4 w-4" />
                                                <span>Edit</span>
                                            </Link>
                                        </DropdownMenuItem>
                                        <DropdownMenuItem 
                                            className="cursor-pointer text-destructive focus:bg-destructive/10 focus:text-destructive"
                                            onClick={() => handleDelete(project.id)}
                                        >
                                            <Trash2 className="mr-2 h-4 w-4" />
                                            <span>Delete</span>
                                        </DropdownMenuItem>
                                    </DropdownMenuContent>
                                </DropdownMenu>
                            </CardHeader>
                            <CardContent>
                                <CardDescription className="line-clamp-2 text-sm mt-1 h-10 mb-4">
                                    {project.description || 'No description provided.'}
                                </CardDescription>
                                
                                <div className="flex flex-col gap-2 mt-2 text-xs text-muted-foreground">
                                    {project.industry && (
                                        <div className="flex items-center gap-1.5">
                                            <Building2 className="h-3.5 w-3.5 opacity-70" />
                                            <span className="truncate">{project.industry}</span>
                                        </div>
                                    )}
                                    {project.location && (
                                        <div className="flex items-center gap-1.5">
                                            <MapPin className="h-3.5 w-3.5 opacity-70" />
                                            <span className="truncate">{project.location}</span>
                                        </div>
                                    )}
                                </div>
                            </CardContent>
                        </Card>
                    ))}
                </div>
            )}

            {/* Pagination placeholder if needed */}
        </AuthenticatedLayout>
    );
}
