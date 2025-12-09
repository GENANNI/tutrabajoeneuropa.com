import { Card } from '@/components/ui/card';
import { Star, Users, Briefcase, TrendingUp } from 'lucide-react';

const testimonials = [
  {
    id: 1,
    name: 'María García',
    role: 'Desarrolladora Full Stack',
    company: 'Tech Solutions',
    image: '👩‍💻',
    text: 'Encontré mi trabajo ideal en Tu Trabajo en Europa. La plataforma es muy fácil de usar y tiene ofertas de calidad.',
    rating: 5,
  },
  {
    id: 2,
    name: 'Juan López',
    role: 'Diseñador UX/UI',
    company: 'Creative Agency',
    image: '👨‍🎨',
    text: 'Excelente plataforma. He recibido varias propuestas interesantes de empresas europeas. Muy recomendado.',
    rating: 5,
  },
  {
    id: 3,
    name: 'Sofia Müller',
    role: 'Project Manager',
    company: 'Global Corp',
    image: '👩‍💼',
    text: 'La mejor decisión que tomé fue registrarme aquí. Ahora trabajo en Berlín en una empresa increíble.',
    rating: 5,
  },
];

const stats = [
  {
    icon: Users,
    value: '15,000+',
    label: 'Usuarios activos',
    color: 'from-blue-600 to-cyan-600',
  },
  {
    icon: Briefcase,
    value: '5,000+',
    label: 'Trabajos disponibles',
    color: 'from-purple-600 to-pink-600',
  },
  {
    icon: TrendingUp,
    value: '85%',
    label: 'Tasa de satisfacción',
    color: 'from-green-600 to-emerald-600',
  },
  {
    icon: Star,
    value: '4.9/5',
    label: 'Calificación promedio',
    color: 'from-yellow-600 to-orange-600',
  },
];

export default function TestimonialsStats() {
  return (
    <div className="py-20 bg-gradient-to-br from-slate-50 to-gray-100 dark:from-slate-900 dark:to-slate-800">
      <div className="container mx-auto px-4 sm:px-6 lg:px-8">
        {/* Estadísticas */}
        <div className="mb-20">
          <h2 className="text-4xl md:text-5xl font-bold text-center mb-16">
            <span className="block text-gray-900 dark:text-white">Números que</span>
            <span className="block bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
              hablan por sí solos
            </span>
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {stats.map((stat, index) => {
              const Icon = stat.icon;
              return (
                <Card
                  key={index}
                  className="bg-white dark:bg-slate-800 border-0 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:scale-105"
                >
                  <div className="p-8">
                    <div className={`bg-gradient-to-r ${stat.color} rounded-lg p-4 mb-4 w-fit`}>
                      <Icon className="w-8 h-8 text-white" />
                    </div>
                    <div className="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-2">
                      {stat.value}
                    </div>
                    <p className="text-gray-600 dark:text-gray-400">{stat.label}</p>
                  </div>
                </Card>
              );
            })}
          </div>
        </div>

        {/* Testimonios */}
        <div>
          <h2 className="text-4xl md:text-5xl font-bold text-center mb-16">
            <span className="block text-gray-900 dark:text-white">Lo que dicen</span>
            <span className="block bg-gradient-to-r from-purple-600 via-pink-600 to-red-600 bg-clip-text text-transparent">
              nuestros usuarios
            </span>
          </h2>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {testimonials.map((testimonial) => (
              <Card
                key={testimonial.id}
                className="bg-white dark:bg-slate-800 border-0 overflow-hidden hover:shadow-2xl transition-all duration-300 hover:scale-105"
              >
                <div className="p-8">
                  {/* Estrellas */}
                  <div className="flex gap-1 mb-4">
                    {[...Array(testimonial.rating)].map((_, i) => (
                      <Star
                        key={i}
                        className="w-5 h-5 fill-yellow-400 text-yellow-400"
                      />
                    ))}
                  </div>

                  {/* Texto */}
                  <p className="text-gray-700 dark:text-gray-300 mb-6 italic">
                    "{testimonial.text}"
                  </p>

                  {/* Autor */}
                  <div className="flex items-center gap-4">
                    <div className="text-4xl">{testimonial.image}</div>
                    <div>
                      <p className="font-bold text-gray-900 dark:text-white">
                        {testimonial.name}
                      </p>
                      <p className="text-sm text-gray-600 dark:text-gray-400">
                        {testimonial.role}
                      </p>
                      <p className="text-xs text-gray-500 dark:text-gray-500">
                        {testimonial.company}
                      </p>
                    </div>
                  </div>
                </div>
              </Card>
            ))}
          </div>
        </div>
      </div>
    </div>
  );
}
