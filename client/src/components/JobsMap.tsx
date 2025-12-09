import { useEffect, useRef, useState } from 'react';
import { Card } from '@/components/ui/card';
import { MapPin, Loader2 } from 'lucide-react';

interface JobLocation {
  id: string;
  title: string;
  company: string;
  location: string;
  lat: number;
  lng: number;
}

interface JobsMapProps {
  jobs?: JobLocation[];
}

export default function JobsMap({ jobs = [] }: JobsMapProps) {
  const mapContainer = useRef<HTMLDivElement>(null);
  const map = useRef<any>(null);
  const [isLoading, setIsLoading] = useState(true);
  const [selectedJob, setSelectedJob] = useState<JobLocation | null>(null);

  // Datos de ejemplo con ubicaciones europeas
  const defaultLocations: JobLocation[] = [
    {
      id: '1',
      title: 'Senior Developer',
      company: 'Tech Corp',
      location: 'Berlin, Germany',
      lat: 52.52,
      lng: 13.405,
    },
    {
      id: '2',
      title: 'Product Manager',
      company: 'StartUp Inc',
      location: 'Amsterdam, Netherlands',
      lat: 52.3676,
      lng: 4.9041,
    },
    {
      id: '3',
      title: 'UX Designer',
      company: 'Design Studio',
      location: 'Barcelona, Spain',
      lat: 41.3851,
      lng: 2.1734,
    },
    {
      id: '4',
      title: 'Full Stack Engineer',
      company: 'Web Solutions',
      location: 'Paris, France',
      lat: 48.8566,
      lng: 2.3522,
    },
    {
      id: '5',
      title: 'Data Scientist',
      company: 'AI Labs',
      location: 'London, UK',
      lat: 51.5074,
      lng: -0.1278,
    },
    {
      id: '6',
      title: 'DevOps Engineer',
      company: 'Cloud Services',
      location: 'Dublin, Ireland',
      lat: 53.3498,
      lng: -6.2603,
    },
  ];

  const locations = jobs.length > 0 ? jobs : defaultLocations;

  useEffect(() => {
    if (!mapContainer.current) return;

    // Crear mapa centrado en Europa
    map.current = new (window as any).google.maps.Map(mapContainer.current, {
      zoom: 4,
      center: { lat: 54.5260, lng: 15.2551 },
      styles: [
        {
          featureType: 'all',
          elementType: 'labels.text.fill',
          stylers: [{ color: '#616161' }],
        },
        {
          featureType: 'all',
          elementType: 'labels.text.stroke',
          stylers: [{ color: '#f5f5f5' }],
        },
        {
          featureType: 'administrative.country',
          elementType: 'geometry.stroke',
          stylers: [{ color: '#cccccc' }],
        },
        {
          featureType: 'water',
          elementType: 'geometry.fill',
          stylers: [{ color: '#e8f4f8' }],
        },
      ],
    });

    // Agregar marcadores
    locations.forEach((job) => {
      const marker = new (window as any).google.maps.Marker({
        position: { lat: job.lat, lng: job.lng },
        map: map.current,
        title: job.title,
        icon: {
          path: (window as any).google.maps.SymbolPath.CIRCLE,
          scale: 10,
          fillColor: '#a855f7',
          fillOpacity: 1,
          strokeColor: '#fff',
          strokeWeight: 2,
        },
      });

      marker.addListener('click', () => {
        setSelectedJob(job);

        // Crear info window
        const infoWindow = new (window as any).google.maps.InfoWindow({
          content: `
            <div style="padding: 10px; font-family: Arial, sans-serif;">
              <h3 style="margin: 0 0 5px 0; color: #a855f7; font-weight: bold;">${job.title}</h3>
              <p style="margin: 0 0 3px 0; color: #666; font-size: 12px;">${job.company}</p>
              <p style="margin: 0; color: #999; font-size: 11px;">${job.location}</p>
            </div>
          `,
        });

        infoWindow.open(map.current, marker);
      });
    });

    setIsLoading(false);
  }, [locations]);

  return (
    <div className="w-full">
      <div className="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {/* Mapa */}
        <div className="lg:col-span-2">
          <Card className="overflow-hidden border-0 h-96 lg:h-[500px]">
            {isLoading && (
              <div className="w-full h-full flex items-center justify-center bg-gray-100 dark:bg-slate-800">
                <Loader2 className="w-8 h-8 animate-spin text-purple-600" />
              </div>
            )}
            <div ref={mapContainer} className="w-full h-full" />
          </Card>
        </div>

        {/* Lista de ubicaciones */}
        <div className="lg:col-span-1">
          <Card className="p-6 bg-white dark:bg-slate-800 border-0 h-96 lg:h-[500px] overflow-y-auto">
            <h3 className="text-lg font-bold mb-4 flex items-center gap-2">
              <MapPin className="w-5 h-5 text-purple-600" />
              Ubicaciones
            </h3>

            <div className="space-y-3">
              {locations.map((job) => (
                <div
                  key={job.id}
                  onClick={() => setSelectedJob(job)}
                  className={`p-3 rounded-lg cursor-pointer transition-all duration-300 ${
                    selectedJob?.id === job.id
                      ? 'bg-gradient-to-r from-purple-600 to-pink-600 text-white'
                      : 'bg-gray-100 dark:bg-slate-700 hover:bg-gray-200 dark:hover:bg-slate-600'
                  }`}
                >
                  <p className="font-semibold text-sm line-clamp-1">{job.title}</p>
                  <p className="text-xs opacity-75 line-clamp-1">{job.company}</p>
                  <p className="text-xs opacity-75 line-clamp-1">{job.location}</p>
                </div>
              ))}
            </div>
          </Card>
        </div>
      </div>

      {/* Información del trabajo seleccionado */}
      {selectedJob && (
        <Card className="mt-6 p-6 bg-white dark:bg-slate-800 border-0">
          <div className="flex justify-between items-start mb-4">
            <div>
              <h2 className="text-2xl font-bold text-gray-900 dark:text-white mb-2">
                {selectedJob.title}
              </h2>
              <p className="text-gray-600 dark:text-gray-400 mb-1">
                {selectedJob.company}
              </p>
              <p className="text-gray-600 dark:text-gray-400 flex items-center gap-2">
                <MapPin className="w-4 h-4" />
                {selectedJob.location}
              </p>
            </div>
          </div>
          <p className="text-gray-700 dark:text-gray-300">
            Haz clic en los marcadores del mapa para ver más detalles sobre cada trabajo disponible en diferentes ciudades europeas.
          </p>
        </Card>
      )}
    </div>
  );
}
