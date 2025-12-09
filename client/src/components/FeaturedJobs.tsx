import { Button } from '@/components/ui/button';
import { Card } from '@/components/ui/card';
import { Badge } from '@/components/ui/badge';
import { ChevronRight, MapPin, Briefcase, DollarSign } from 'lucide-react';
import { useEffect, useState } from 'react';
import { jobAPI } from '@/lib/api';
import { useLocation } from 'wouter';

interface Job {
  id: string;
  title: string;
  company: string;
  location: string;
  salary?: string;
  description: string;
}

export default function FeaturedJobs() {
  const [jobs, setJobs] = useState<Job[]>([]);
  const [isLoading, setIsLoading] = useState(true);
  const [, navigate] = useLocation();

  useEffect(() => {
    const loadJobs = async () => {
      try {
        const data = await jobAPI.list();
        setJobs((Array.isArray(data) ? data : []).slice(0, 6));
      } catch (error) {
        console.error('Error loading jobs:', error);
      } finally {
        setIsLoading(false);
      }
    };

    loadJobs();
  }, []);

  const handleViewJob = (jobId: string) => {
    navigate(`/job-search?id=${jobId}`);
  };

  return (
    <section className="py-20 bg-white dark:bg-slate-900">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Encabezado */}
        <div className="text-center mb-16">
          <h2 className="text-4xl md:text-5xl font-bold mb-4">
            <span className="block text-gray-900 dark:text-white">Trabajos</span>
            <span className="block bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
              Destacados
            </span>
          </h2>
          <p className="text-lg text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
            Descubre las mejores oportunidades seleccionadas especialmente para ti
          </p>
        </div>

        {/* Grid de trabajos */}
        {isLoading ? (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {[...Array(6)].map((_, i) => (
              <div key={i} className="bg-gray-200 dark:bg-slate-800 rounded-xl h-80 animate-pulse" />
            ))}
          </div>
        ) : (
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-12">
            {jobs.map((job) => (
              <Card
                key={job.id}
                className="group overflow-hidden hover:shadow-2xl transition-all duration-300 hover:scale-105 bg-gradient-to-br from-white to-gray-50 dark:from-slate-800 dark:to-slate-900 border-0"
              >
                <div className="p-6 h-full flex flex-col">
                  {/* Header */}
                  <div className="mb-4">
                    <div className="flex items-start justify-between mb-3">
                      <div className="flex-1">
                        <h3 className="text-xl font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-purple-600 transition-colors">
                          {job.title}
                        </h3>
                        <p className="text-sm text-gray-600 dark:text-gray-400 mt-1">
                          {job.company}
                        </p>
                      </div>
                      <Badge className="bg-gradient-to-r from-purple-600 to-pink-600 text-white ml-2">
                        Nuevo
                      </Badge>
                    </div>
                  </div>

                  {/* Información */}
                  <div className="space-y-3 mb-6 flex-1">
                    <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                      <MapPin className="w-4 h-4 text-blue-500" />
                      <span>{job.location}</span>
                    </div>
                    {job.salary && (
                      <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                        <DollarSign className="w-4 h-4 text-green-500" />
                        <span>{job.salary}</span>
                      </div>
                    )}
                    <div className="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                      <Briefcase className="w-4 h-4 text-purple-500" />
                      <span>Tiempo completo</span>
                    </div>
                  </div>

                  {/* Descripción */}
                  <p className="text-sm text-gray-600 dark:text-gray-400 line-clamp-3 mb-6">
                    {job.description}
                  </p>

                  {/* Botón */}
                  <Button
                    onClick={() => handleViewJob(job.id)}
                    className="w-full bg-gradient-to-r from-purple-600 to-pink-600 text-white hover:shadow-lg transition-all duration-300 group-hover:scale-105"
                  >
                    Ver detalles
                    <ChevronRight className="w-4 h-4 ml-2" />
                  </Button>
                </div>
              </Card>
            ))}
          </div>
        )}

        {/* CTA */}
        <div className="text-center">
          <Button
            onClick={() => navigate('/job-search')}
            size="lg"
            className="bg-gradient-to-r from-blue-600 to-cyan-600 text-white hover:shadow-lg transition-all duration-300 hover:scale-105"
          >
            Ver todos los trabajos
            <ChevronRight className="w-5 h-5 ml-2" />
          </Button>
        </div>
      </div>
    </section>
  );
}
